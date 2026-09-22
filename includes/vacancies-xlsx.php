<?php
/** Minimal XLSX writer for the vacancy matrix. No external Composer dependency. */

function vacanciesXlsxEscape($value)
{
    $value = (string) $value;
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value);
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function vacanciesXlsxColumnName($number)
{
    $number = (int) $number;
    $name = '';
    while ($number > 0) {
        $number--;
        $name = chr(65 + ($number % 26)) . $name;
        $number = (int) floor($number / 26);
    }
    return $name;
}

function vacanciesXlsxStringCell($ref, $value, $style)
{
    return '<c r="'.vacanciesXlsxEscape($ref).'" s="'.(int)$style.'" t="inlineStr"><is><t xml:space="preserve">'.vacanciesXlsxEscape($value).'</t></is></c>';
}

function vacanciesXlsxNumberCell($ref, $value, $style)
{
    return '<c r="'.vacanciesXlsxEscape($ref).'" s="'.(int)$style.'"><v>'.(int)$value.'</v></c>';
}

function vacanciesXlsxBlankCell($ref, $style)
{
    return '<c r="'.vacanciesXlsxEscape($ref).'" s="'.(int)$style.'"/>';
}

function vacanciesXlsxStylesXml()
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<fonts count="3">'
          .'<font><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
          .'<font><b/><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
          .'<font><b/><color rgb="FFFFFFFF"/><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
        .'</fonts>'
        .'<fills count="7">'
          .'<fill><patternFill patternType="none"/></fill>'
          .'<fill><patternFill patternType="gray125"/></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FF334155"/><bgColor indexed="64"/></patternFill></fill>'
        .'</fills>'
        .'<borders count="2">'
          .'<border><left/><right/><top/><bottom/><diagonal/></border>'
          .'<border><left style="thin"><color rgb="FFD9E2EC"/></left><right style="thin"><color rgb="FFD9E2EC"/></right><top style="thin"><color rgb="FFD9E2EC"/></top><bottom style="thin"><color rgb="FFD9E2EC"/></bottom><diagonal/></border>'
        .'</borders>'
        .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        .'<cellXfs count="9">'
          .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
          .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="1" fillId="5" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="center"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
          .'<xf numFmtId="0" fontId="2" fillId="6" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="5" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        .'</cellXfs>'
        .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
        .'</styleSheet>';
}

function vacanciesXlsxMatrixSheetXml($data)
{
    $schools = $data['schools'];
    $specialties = $data['specialties'];
    $submitted = $data['submitted'];
    $values = $data['values'];
    $schoolCount = count($schools);
    $lastCol = vacanciesXlsxColumnName($schoolCount + 2);
    $lastRow = count($specialties) + 2;

    $rows = array();
    $header = vacanciesXlsxStringCell('A1', 'ΕΙΔΙΚΟΤΗΤΑ', 1);
    foreach ($schools as $idx => $school) {
        $col = vacanciesXlsxColumnName($idx + 2);
        $header .= vacanciesXlsxStringCell($col.'1', $school['export_label'], 1);
    }
    $header .= vacanciesXlsxStringCell($lastCol.'1', 'ΣΥΝ', 6);
    $rows[] = '<row r="1" ht="58" customHeight="1">'.$header.'</row>';

    $schoolTotals = array();
    foreach ($schools as $school) $schoolTotals[(int)$school['id']] = 0;
    $grandTotal = 0;
    $rowNo = 2;
    foreach ($specialties as $sp) {
        $spId = (int) $sp['id'];
        $rowXml = vacanciesXlsxStringCell('A'.$rowNo, $sp['export_label'], 2);
        $rowTotal = 0;
        foreach ($schools as $idx => $school) {
            $schoolId = (int) $school['id'];
            $col = vacanciesXlsxColumnName($idx + 2);
            if (!isset($submitted[$schoolId])) {
                $rowXml .= vacanciesXlsxBlankCell($col.$rowNo, 7);
                continue;
            }
            $value = isset($values[$spId]) && isset($values[$spId][$schoolId]) ? (int) $values[$spId][$schoolId] : 0;
            $style = $value < 0 ? 3 : ($value > 0 ? 4 : 5);
            $rowXml .= vacanciesXlsxNumberCell($col.$rowNo, $value, $style);
            $rowTotal += $value;
            $schoolTotals[$schoolId] += $value;
        }
        $rowXml .= vacanciesXlsxNumberCell($lastCol.$rowNo, $rowTotal, 6);
        $grandTotal += $rowTotal;
        $rows[] = '<row r="'.$rowNo.'">'.$rowXml.'</row>';
        $rowNo++;
    }

    $totalXml = vacanciesXlsxStringCell('A'.$rowNo, 'ΣΥΝΟΛΟ', 6);
    foreach ($schools as $idx => $school) {
        $schoolId = (int) $school['id'];
        $col = vacanciesXlsxColumnName($idx + 2);
        if (!isset($submitted[$schoolId])) {
            $totalXml .= vacanciesXlsxBlankCell($col.$rowNo, 7);
        } else {
            $totalXml .= vacanciesXlsxNumberCell($col.$rowNo, $schoolTotals[$schoolId], 6);
        }
    }
    $totalXml .= vacanciesXlsxNumberCell($lastCol.$rowNo, $grandTotal, 6);
    $rows[] = '<row r="'.$rowNo.'">'.$totalXml.'</row>';

    $schoolColsEnd = vacanciesXlsxColumnName($schoolCount + 1);
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<dimension ref="A1:'.$lastCol.$lastRow.'"/>'
        .'<sheetViews><sheetView workbookViewId="0"><pane xSplit="1" ySplit="1" topLeftCell="B2" activePane="bottomRight" state="frozen"/></sheetView></sheetViews>'
        .'<sheetFormatPr defaultRowHeight="16"/>'
        .'<cols><col min="1" max="1" width="22" customWidth="1"/><col min="2" max="'.($schoolCount+1).'" width="15" customWidth="1"/><col min="'.($schoolCount+2).'" max="'.($schoolCount+2).'" width="11" customWidth="1"/></cols>'
        .'<sheetData>'.implode('', $rows).'</sheetData>'
        .'<autoFilter ref="A1:'.$schoolColsEnd.'1"/>'
        .'</worksheet>';
}

function vacanciesXlsxNotesSheetXml($data)
{
    $schools = $data['schools'];
    $submitted = $data['submitted'];
    $notes = $data['notes'];
    $rows = array();
    $headers = array('Σχολείο','Κωδικός','Κατάσταση','Αναθεώρηση','Υποβολή','ΠΑΡΑΤΗΡΗΣΕΙΣ');
    $headerXml = '';
    foreach ($headers as $idx => $label) $headerXml .= vacanciesXlsxStringCell(vacanciesXlsxColumnName($idx+1).'1', $label, 1);
    $rows[] = '<row r="1" ht="28" customHeight="1">'.$headerXml.'</row>';
    $r = 2;
    foreach ($schools as $school) {
        $schoolId = (int) $school['id'];
        $isSubmitted = isset($submitted[$schoolId]);
        $note = isset($notes[$schoolId]) ? $notes[$schoolId] : array('school_note'=>'','submitted_at'=>'','revision_no'=>0);
        $cells = vacanciesXlsxStringCell('A'.$r, $school['export_label'], 8)
            .vacanciesXlsxStringCell('B'.$r, $school['ministry_code'], 8)
            .vacanciesXlsxStringCell('C'.$r, $isSubmitted ? 'Οριστική' : 'Δεν υπέβαλε', 8);
        if ($isSubmitted) {
            $cells .= vacanciesXlsxNumberCell('D'.$r, (int)$note['revision_no'], 5)
                .vacanciesXlsxStringCell('E'.$r, (string)$note['submitted_at'], 8)
                .vacanciesXlsxStringCell('F'.$r, (string)$note['school_note'], 8);
        } else {
            $cells .= vacanciesXlsxBlankCell('D'.$r, 7).vacanciesXlsxBlankCell('E'.$r, 7).vacanciesXlsxBlankCell('F'.$r, 7);
        }
        $rows[] = '<row r="'.$r.'">'.$cells.'</row>';
        $r++;
    }
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<dimension ref="A1:F'.max(2,$r-1).'"/>'
        .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
        .'<sheetFormatPr defaultRowHeight="18"/>'
        .'<cols><col min="1" max="1" width="28" customWidth="1"/><col min="2" max="2" width="14" customWidth="1"/><col min="3" max="3" width="16" customWidth="1"/><col min="4" max="4" width="12" customWidth="1"/><col min="5" max="5" width="22" customWidth="1"/><col min="6" max="6" width="70" customWidth="1"/></cols>'
        .'<sheetData>'.implode('', $rows).'</sheetData><autoFilter ref="A1:F1"/>'
        .'</worksheet>';
}

function vacanciesXlsxDosTimeDate()
{
    $t = getdate();
    $year = max(1980, (int) $t['year']);
    $dosTime = (((int)$t['hours'] & 31) << 11) | (((int)$t['minutes'] & 63) << 5) | (((int)$t['seconds'] >> 1) & 31);
    $dosDate = (($year - 1980) << 9) | (((int)$t['mon'] & 15) << 5) | ((int)$t['mday'] & 31);
    return array($dosTime, $dosDate);
}

/** Build a small ZIP archive in pure PHP, used when php-zip/ZipArchive is unavailable. */
function vacanciesXlsxZipBinary($files)
{
    list($dosTime, $dosDate) = vacanciesXlsxDosTimeDate();
    $local = '';
    $central = '';
    $offset = 0;
    $count = 0;
    foreach ($files as $name => $data) {
        $name = (string) $name;
        $data = (string) $data;
        $method = function_exists('gzdeflate') ? 8 : 0;
        $compressed = $method === 8 ? gzdeflate($data, 6) : $data;
        if ($compressed === false) { $method = 0; $compressed = $data; }
        $crc = crc32($data);
        if ($crc < 0) $crc += 4294967296;
        $compressedSize = strlen($compressed);
        $size = strlen($data);
        $nameLen = strlen($name);
        $flags = 0;

        $localHeader = pack('VvvvvvVVVvv', 0x04034b50, 20, $flags, $method, $dosTime, $dosDate, $crc, $compressedSize, $size, $nameLen, 0) . $name;
        $local .= $localHeader . $compressed;

        $central .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, $flags, $method, $dosTime, $dosDate, $crc, $compressedSize, $size, $nameLen, 0, 0, 0, 0, 0, $offset) . $name;
        $offset += strlen($localHeader) + $compressedSize;
        $count++;
    }
    $centralOffset = strlen($local);
    $centralSize = strlen($central);
    $end = pack('VvvvvVVv', 0x06054b50, 0, 0, $count, $count, $centralSize, $centralOffset, 0);
    return $local . $central . $end;
}

function vacanciesXlsxBuild($data)
{
    $created = gmdate('Y-m-d\\TH:i:s\\Z');
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>',
        'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:title>'.vacanciesXlsxEscape($data['round']['title']).'</dc:title><dc:creator>Καταγραφή κενών σχολικών μονάδων</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">'.$created.'</dcterms:created></cp:coreProperties>',
        'docProps/app.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Καταγραφή κενών σχολικών μονάδων</Application></Properties>',
        'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="ΣΥΝ" sheetId="1" r:id="rId1"/><sheet name="Παρατηρήσεις" sheetId="2" r:id="rId2"/></sheets><calcPr calcId="191029" fullCalcOnLoad="1"/></workbook>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
        'xl/styles.xml' => vacanciesXlsxStylesXml(),
        'xl/worksheets/sheet1.xml' => vacanciesXlsxMatrixSheetXml($data),
        'xl/worksheets/sheet2.xml' => vacanciesXlsxNotesSheetXml($data),
    );

    if (class_exists('ZipArchive')) {
        $tmp = tempnam(sys_get_temp_dir(), 'vacxlsx_');
        if (!$tmp) return array(false, 'Δεν δημιουργήθηκε προσωρινό αρχείο εξαγωγής.');
        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            return array(false, 'Δεν δημιουργήθηκε το αρχείο Excel.');
        }
        foreach ($files as $name => $contents) $zip->addFromString($name, $contents);
        $zip->close();
        $binary = @file_get_contents($tmp);
        @unlink($tmp);
        if ($binary === false) return array(false, 'Δεν διαβάστηκε το προσωρινό αρχείο Excel.');
        return array(true, $binary);
    }

    return array(true, vacanciesXlsxZipBinary($files));
}
