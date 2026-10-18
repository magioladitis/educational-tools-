/** Legal wording contract: distinguish polytéknos status from child points. */
const assert=require('assert');
const fs=require('fs');
const path=require('path');
const src=fs.readFileSync(path.join(__dirname,'../includes/transfer-children-documents.js'),'utf8');
for(const token of ['ΠΟΛΥΤΕΚΝΙΑ (άρθρο 6 ν. 3454/2006)','23 έτη','25 έτη','67%','τέσσερα τέκνα','τρία τέκνα','διατηρείται ισοβίως','δεν ταυτίζεται με τις μονάδες μετάθεσης'])
  assert(src.includes(token), 'Missing polyteknia guidance: '+token);
assert(!src.includes('ΠΟΛΥΤΕΚΝΟΙ: ελέγξτε ειδικά ηλικιακά όρια'),'Old vague wording must not remain');
console.log('PASS polyteknia copy contract (8 conditions)');
