from pathlib import Path
root=Path(__file__).resolve().parent.parent
for name in ['permanent-leaves-data.php','substitute-leaves-data.php']:
    s=(root/'includes'/name).read_text()
    block=s.split("'id' => 'eklogiko'",1)[1].split("'id' =>",1)[0]
    for expected in ['άνω των 200 έως 400','400 χλμ. και άνω','χωρίς οδική πρόσβαση','έως 3 εργάσιμες','υπεύθυνης δήλωσης','υπηρεσιακών αναγκών',"'ypes_electoral_right'"]:
        assert expected in block,(name,expected)
print('PASS: 2 electoral leave entries x 7 conditions')
