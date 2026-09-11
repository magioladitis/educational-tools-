const fs=require('fs'),path=require('path');
const ui=fs.readFileSync(path.join(__dirname,'..','includes','staffing-simulator-ui.js'),'utf8');
let pass=0,fail=0;function check(n,c){if(c){console.log('PASS: '+n);pass++;}else{console.log('FAIL: '+n);fail++;}}
const m=ui.match(/function compactRepeatedFormState\(form,prefix,payloadName\)\{[\s\S]*?\n  \}\n  function installExplicitRequestGate/);
check('compact serializer function found',!!m);
if(m){
  const src=m[0].replace(/\n  function installExplicitRequestGate$/,'');
  // eslint-disable-next-line no-eval
  eval(src);
  const hidden={name:'allocation_payload_json',value:'',disabled:false};
  const fields=[
    {name:'allocation_slot_id[]',value:'slot-A',disabled:false},
    {name:'allocation_person_id[]',value:'p-1',disabled:false},
    {name:'allocation_hours[]',value:'2',disabled:false},
    {name:'allocation_slot_id[]',value:'',disabled:false},
    {name:'allocation_person_id[]',value:'SHOULD-NOT-SHIFT',disabled:true},
    {name:'allocation_hours[]',value:'0',disabled:false}
  ];
  const form={
    querySelector(sel){return sel==='input[name="allocation_payload_json"]'?hidden:null;},
    querySelectorAll(sel){return sel==='[name^="allocation_"]'?fields:[];}
  };
  compactRepeatedFormState(form,'allocation_','allocation_payload_json');
  const payload=JSON.parse(hidden.value||'{}');
  check('slot/person/hours arrays keep equal length',payload.allocation_slot_id.length===2&&payload.allocation_person_id.length===2&&payload.allocation_hours.length===2);
  check('disabled person reserves blank position',payload.allocation_person_id[1]==='');
  check('first row remains aligned',payload.allocation_slot_id[0]==='slot-A'&&payload.allocation_person_id[0]==='p-1'&&payload.allocation_hours[0]==='2');
  check('blank row remains blank rather than shifting next arrays',payload.allocation_slot_id[1]===''&&payload.allocation_hours[1]==='0');
  check('repeated controls disabled only after serialization',fields.every(f=>f.disabled===true));
}
console.log(`RESULT ${pass} PASS / ${fail} FAIL`);process.exit(fail?1:0);
