const app = document.querySelector('#app');
const home = document.querySelector('#home');
const storageKey = 'eduToolsB1QuizProgressV1:' + window.location.pathname;
const greek = new Intl.NumberFormat('el-GR');
let progressData = { attempts: 0, best: null, mistakes: [] };
let settings = { mode: 'practice', topic: 'all', count: '10' };
let session = null;

try {
  const saved = JSON.parse(localStorage.getItem(storageKey));
  if (saved && Array.isArray(saved.mistakes) && Number.isInteger(saved.attempts)) {
    progressData = saved;
  }
} catch {
  // Quizzes remain usable when local storage is unavailable.
}

function node(tag, text, className) {
  const element = document.createElement(tag);
  if (text != null) element.textContent = text;
  if (className) element.className = className;
  return element;
}

function button(text, className, action) {
  const element = node('button', text, className);
  element.type = 'button';
  element.addEventListener('click', action);
  return element;
}

function show(element, canGoHome) {
  app.replaceChildren(element);
  home.hidden = !canGoHome;
  window.scrollTo(0, 0);
  app.focus({ preventScroll: true });
}

function shuffle(items) {
  const result = [...items];
  for (let index = result.length - 1; index > 0; index--) {
    const other = Math.floor(Math.random() * (index + 1));
    [result[index], result[other]] = [result[other], result[index]];
  }
  return result;
}

function poolFor(topic) {
  return topic === 'all' ? bank : bank.filter(question => question.topic === topic);
}

function mistakesPool() {
  return bank.filter(question => progressData.mistakes.includes(question.id));
}

function percentage(value) {
  return greek.format(Math.round(value * 100)) + '%';
}

function showSetup() {
  session = null;
  show(document.querySelector('#setup').content.cloneNode(true), false);
  const form = document.querySelector('#settings');
  const topicSelect = document.querySelector('#topic');
  const countSelect = document.querySelector('#count');
  const topicList = document.querySelector('#topics');
  for (const [key, topic] of Object.entries(topics)) {
    const count = poolFor(key).length;
    topicSelect.add(new Option(topic.name, key));
    const item = node('div', null, 'topic-line');
    item.append(node('span', topic.name), node('b', count));
    topicList.append(item);
  }
  form.elements.mode.value = settings.mode;
  topicSelect.value = settings.topic;
  function updateCounts(preferred) {
    const total = poolFor(topicSelect.value).length;
    const counts = [5, 10, 15, 20, 30].filter(count => count < total);
    countSelect.replaceChildren(...counts.map(count => new Option(count, count)), new Option('Όλες (' + total + ')', 'all'));
    countSelect.value = preferred;
    if (countSelect.selectedIndex < 0) countSelect.value = 'all';
    document.querySelector('#available').textContent = total + ' διαθέσιμες ερωτήσεις';
  }
  updateCounts(settings.count);
  topicSelect.addEventListener('change', () => updateCounts(countSelect.value));
  document.querySelector('#runs').textContent = progressData.attempts;
  document.querySelector('#best').textContent = progressData.best === null ? '–' : percentage(progressData.best);
  document.querySelector('#bank-count').textContent = bank.length;
  const mistakes = mistakesPool();
  document.querySelector('#retry-info').textContent = mistakes.length
    ? 'Ερωτήσεις για επανάληψη: ' + mistakes.length + '.'
    : 'Οι ερωτήσεις που θα απαντήσεις λάθος εμφανίζονται εδώ.';
  const retry = document.querySelector('#retry');
  retry.disabled = mistakes.length === 0;
  retry.addEventListener('click', () => start(mistakesPool(), 'practice', true));
  form.addEventListener('submit', event => {
    event.preventDefault();
    settings = { mode: form.elements.mode.value, topic: topicSelect.value, count: countSelect.value };
    const pool = poolFor(settings.topic);
    start(pool, settings.mode, false, settings.count === 'all' ? pool.length : Number(settings.count));
  });
}

function start(pool, mode, retry, count = pool.length) {
  if (!pool.length) return;
  session = {
    questions: shuffle(pool).slice(0, count).map(question => ({
      ...question,
      order: shuffle(question.options.map((_, index) => index)),
      answer: null,
      checked: false
    })),
    position: 0,
    mode,
    retry,
    finished: false
  };
  showQuestion();
}

function modeName() {
  if (session.retry) return 'Επανάληψη λαθών';
  return session.mode === 'exam' ? 'Δοκιμαστικό τεστ' : 'Εξάσκηση';
}

function showQuestion(focusFeedback = false) {
  const { questions, position, mode } = session;
  const question = questions[position];
  const practice = mode === 'practice';
  const isLast = position + 1 === questions.length;
  const frame = node('section', null, 'frame quiz-frame');
  const meta = node('div', null, 'quiz-meta');
  meta.append(node('span', modeName()), node('span', 'Ερώτηση ' + (position + 1) + ' από ' + questions.length));
  const bar = node('progress');
  bar.max = questions.length;
  bar.value = questions.filter(item => practice ? item.checked : item.answer !== null).length;
  bar.setAttribute('aria-label', 'Απαντημένες ερωτήσεις');
  const card = node('section', null, 'card quiz-card');
  const prompt = node('h1', question.prompt, 'question');
  prompt.id = 'question-prompt';
  const form = node('form');
  const options = node('div', null, 'option-list');
  options.setAttribute('role', 'radiogroup');
  options.setAttribute('aria-labelledby', prompt.id);
  const actions = node('div', null, 'actions');
  const submit = node('button', practice && !question.checked ? 'Έλεγχος απάντησης' : isLast ? 'Αποτελέσματα' : 'Επόμενη ερώτηση', 'primary');
  submit.type = 'submit';
  submit.disabled = practice && question.answer === null;
  for (const index of question.order) {
    const option = node('label', null, 'option');
    const radio = node('input');
    radio.type = 'radio';
    radio.name = 'answer';
    radio.value = index;
    radio.checked = index === question.answer;
    radio.disabled = question.checked;
    radio.addEventListener('change', () => {
      question.answer = index;
      submit.disabled = false;
      if (!practice) bar.value = questions.filter(item => item.answer !== null).length;
    });
    option.append(radio, node('span', question.options[index]));
    if (question.checked && (index === question.correct || index === question.answer)) {
      const correct = index === question.correct;
      option.classList.add(correct ? 'is-correct' : 'is-wrong');
      option.append(node('span', correct ? 'Σωστή' : 'Επιλογή σου', 'mark'));
    }
    options.append(option);
  }
  let feedback;
  form.append(options);
  if (question.checked) {
    const correct = question.answer === question.correct;
    feedback = node('div', null, correct ? 'feedback' : 'feedback wrong');
    feedback.tabIndex = -1;
    feedback.append(node('strong', correct ? 'Σωστή απάντηση' : 'Λάθος απάντηση'), node('p', question.explanation), node('p', question.source, 'muted'));
    form.append(feedback);
  }
  if (!practice && position > 0) {
    actions.append(button('Προηγούμενη', 'secondary', () => {
      session.position--;
      showQuestion();
    }));
  }
  actions.append(submit);
  form.append(actions);
  if (!practice && !isLast) {
    const extra = node('div', null, 'position-controls');
    extra.append(button('Ολοκλήρωση τεστ', 'secondary', finishExam));
    form.append(extra);
  }
  form.addEventListener('submit', event => {
    event.preventDefault();
    if (practice && !question.checked) {
      if (question.answer === null) return;
      question.checked = true;
      showQuestion(true);
      return;
    }
    if (isLast) {
      if (practice) finish();
      else finishExam();
      return;
    }
    session.position++;
    showQuestion();
  });
  card.append(node('span', topics[question.topic].name, 'pill'), prompt, form);
  frame.append(meta, bar, card);
  show(frame, true);
  if (focusFeedback) feedback?.focus();
}

function finishExam() {
  const unanswered = session.questions.filter(question => question.answer === null).length;
  if (unanswered && !window.confirm('Έχεις ' + unanswered + ' αναπάντητες ερωτήσεις. Θέλεις να ολοκληρώσεις το τεστ;')) return;
  finish();
}

function finish() {
  if (session.finished) return;
  session.finished = true;
  const questions = session.questions;
  const correct = questions.filter(question => question.answer === question.correct).length;
  const score = correct / questions.length;
  const mistakes = new Set(progressData.mistakes);
  for (const question of questions) {
    if (question.answer === question.correct) mistakes.delete(question.id);
    else mistakes.add(question.id);
  }
  progressData = {
    attempts: progressData.attempts + 1,
    best: Math.max(progressData.best ?? 0, score),
    mistakes: [...mistakes]
  };
  try {
    localStorage.setItem(storageKey, JSON.stringify(progressData));
  } catch {
    // The results stay available until the page is closed.
  }

  const frame = node('div', null, 'frame quiz-frame');
  const results = node('section', null, 'card results');
  const scoreLine = node('p', null, 'score');
  scoreLine.append(node('strong', correct + ' / ' + questions.length), node('span', 'σωστές απαντήσεις · ' + percentage(score)));
  const breakdown = node('div', null, 'breakdown');
  for (const [key, topic] of Object.entries(topics)) {
    const group = questions.filter(question => question.topic === key);
    if (group.length) {
      const row = node('div');
      row.append(node('span', topic.name), node('strong', group.filter(question => question.answer === question.correct).length + ' / ' + group.length));
      breakdown.append(row);
    }
  }
  const actions = node('div', null, 'actions');
  actions.append(button('Νέα προσπάθεια', 'primary', showSetup));
  const missed = questions.filter(question => question.answer !== question.correct);
  if (missed.length) actions.append(button('Επανάληψη λαθών', 'secondary', () => start(missed, 'practice', true)));
  results.append(node('span', modeName(), 'pill'), node('h1', 'Αποτελέσματα'), scoreLine, breakdown, actions);
  const review = node('section', null, 'card review');
  const list = node('div');
  const filter = node('label', null, 'review-filter');
  const onlyMistakes = node('input');
  onlyMistakes.type = 'checkbox';
  filter.append(onlyMistakes, node('span', 'Μόνο λάθη και αναπάντητες'));
  function renderReview() {
    list.replaceChildren();
    questions.forEach((question, index) => {
      const correctAnswer = question.answer === question.correct;
      if (onlyMistakes.checked && correctAnswer) return;
      const details = node('details');
      const status = correctAnswer ? 'Σωστή' : question.answer === null ? 'Αναπάντητη' : 'Λάθος';
      details.append(
        node('summary', (index + 1) + '. ' + status + ' · ' + topics[question.topic].name, correctAnswer ? 'good' : 'bad'),
        node('p', question.prompt),
        node('p', 'Η απάντησή σου — ' + (question.options[question.answer] ?? 'Καμία απάντηση.')),
        node('p', 'Σωστή απάντηση — ' + question.options[question.correct]),
        node('p', question.explanation),
        node('p', question.source, 'muted')
      );
      list.append(details);
    });
    if (!list.children.length) list.append(node('p', 'Όλες οι απαντήσεις ήταν σωστές.', 'muted'));
  }
  onlyMistakes.addEventListener('change', renderReview);
  renderReview();
  review.append(node('h2', 'Ανασκόπηση απαντήσεων'), filter, list);
  frame.append(results, review);
  show(frame, true);
}

home.addEventListener('click', () => {
  if (session && !session.finished && !window.confirm('Θέλεις να επιστρέψεις στην αρχική και να τερματίσεις αυτή την προσπάθεια;')) return;
  showSetup();
});
showSetup();
