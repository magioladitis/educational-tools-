<?php
/** Minimal XLSX writer for the service-change timeline. No external Composer dependency. */

function serviceTimelineXlsxEscape($value)
{
    $value = (string) $value;
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value);
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function serviceTimelineXlsxColumnName($number)
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

function serviceTimelineXlsxStringCell($ref, $value, $style)
{
    return '<c r="'.serviceTimelineXlsxEscape($ref).'" s="'.(int)$style.'" t="inlineStr"><is><t xml:space="preserve">'.serviceTimelineXlsxEscape($value).'</t></is></c>';
}

function serviceTimelineXlsxBlankCell($ref, $style)
{
    return '<c r="'.serviceTimelineXlsxEscape($ref).'" s="'.(int)$style.'"/>';
}

function serviceTimelineXlsxStylesXml()
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<fonts count="4">'
          .'<font><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
          .'<font><b/><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
          .'<font><b/><color rgb="FFFFFFFF"/><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
          .'<font><color rgb="FF1D4ED8"/><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
        .'</fonts>'
        .'<fills count="5">'
          .'<fill><patternFill patternType="none"/></fill>'
          .'<fill><patternFill patternType="gray125"/></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFB45309"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFFFF7ED"/><bgColor indexed="64"/></patternFill></fill>'
          .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/><bgColor indexed="64"/></patternFill></fill>'
        .'</fills>'
        .'<borders count="2">'
          .'<border><left/><right/><top/><bottom/><diagonal/></border>'
          .'<border><left style="thin"><color rgb="FFD9E2EC"/></left><right style="thin"><color rgb="FFD9E2EC"/></right><top style="thin"><color rgb="FFD9E2EC"/></top><bottom style="thin"><color rgb="FFD9E2EC"/></bottom><diagonal/></border>'
        .'</borders>'
        .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        .'<cellXfs count="7">'
          .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
          .'<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
          .'<xf numFmtId="0" fontId="1" fillId="4" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        .'</cellXfs>'
        .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
        .'</styleSheet>';
}

function serviceTimelineXlsxChronologySheetXml($data)
{
    $years = isset($data['history_years']) && is_array($data['history_years']) ? $data['history_years'] : array();
    $groups = isset($data['groups']) && is_array($data['groups']) ? $data['groups'] : array();
    $events = isset($data['events']) && is_array($data['events']) ? $data['events'] : array();
    $headers = array_merge(array('Κατηγορία', 'Διαδικασία'), $years, array('Τελευταία τιμή', 'Παρατηρήσεις'));
    $lastCol = serviceTimelineXlsxColumnName(count($headers));
    $rows = array();

    $headerXml = '';
    foreach ($headers as $idx => $label) {
        $headerXml .= serviceTimelineXlsxStringCell(serviceTimelineXlsxColumnName($idx + 1).'1', $label, 1);
    }
    $rows[] = '<row r="1" ht="34" customHeight="1">'.$headerXml.'</row>';

    $rowNo = 2;
    foreach ($events as $event) {
        $groupKey = isset($event['group']) ? (string)$event['group'] : '';
        $groupLabel = isset($groups[$groupKey]) ? $groups[$groupKey] : $groupKey;
        $history = isset($event['history']) && is_array($event['history']) ? $event['history'] : array();
        $cells = serviceTimelineXlsxStringCell('A'.$rowNo, $groupLabel, 2)
            . serviceTimelineXlsxStringCell('B'.$rowNo, isset($event['title']) ? $event['title'] : '', 3);
        foreach ($years as $idx => $year) {
            $col = serviceTimelineXlsxColumnName($idx + 3);
            $value = isset($history[$idx]) && $history[$idx] !== null ? (string)$history[$idx] : '';
            $cells .= $value === '' ? serviceTimelineXlsxBlankCell($col.$rowNo, 4) : serviceTimelineXlsxStringCell($col.$rowNo, $value, 4);
        }
        $lastValueCol = serviceTimelineXlsxColumnName(3 + count($years));
        $noteCol = serviceTimelineXlsxColumnName(4 + count($years));
        $cells .= serviceTimelineXlsxStringCell($lastValueCol.$rowNo, isset($event['latest']) ? $event['latest'] : '', 4)
            . serviceTimelineXlsxStringCell($noteCol.$rowNo, isset($event['note']) ? $event['note'] : '', 3);
        $rows[] = '<row r="'.$rowNo.'" ht="28" customHeight="1">'.$cells.'</row>';
        $rowNo++;
    }

    $yearStart = 3;
    $yearEnd = 2 + count($years);
    $latestColNo = $yearEnd + 1;
    $notesColNo = $yearEnd + 2;
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<dimension ref="A1:'.$lastCol.max(1, $rowNo - 1).'"/>'
        .'<sheetViews><sheetView workbookViewId="0"><pane xSplit="2" ySplit="1" topLeftCell="C2" activePane="bottomRight" state="frozen"/></sheetView></sheetViews>'
        .'<sheetFormatPr defaultRowHeight="18"/>'
        .'<cols>'
          .'<col min="1" max="1" width="18" customWidth="1"/>'
          .'<col min="2" max="2" width="42" customWidth="1"/>'
          .'<col min="'.$yearStart.'" max="'.$yearEnd.'" width="16" customWidth="1"/>'
          .'<col min="'.$latestColNo.'" max="'.$latestColNo.'" width="24" customWidth="1"/>'
          .'<col min="'.$notesColNo.'" max="'.$notesColNo.'" width="62" customWidth="1"/>'
        .'</cols>'
        .'<sheetData>'.implode('', $rows).'</sheetData>'
        .'<autoFilter ref="A1:'.$lastCol.max(1, $rowNo - 1).'"/>'
        .'</worksheet>';
}

function serviceTimelineXlsxSourcesSheetXml($data)
{
    $groups = isset($data['groups']) && is_array($data['groups']) ? $data['groups'] : array();
    $events = isset($data['events']) && is_array($data['events']) ? $data['events'] : array();
    $headers = array('Κατηγορία', 'Διαδικασία', 'Έτος / κύκλος', 'Πηγή', 'Σύνδεσμος');
    $rows = array();
    $headerXml = '';
    foreach ($headers as $idx => $label) {
        $headerXml .= serviceTimelineXlsxStringCell(serviceTimelineXlsxColumnName($idx + 1).'1', $label, 1);
    }
    $rows[] = '<row r="1" ht="34" customHeight="1">'.$headerXml.'</row>';

    $rowNo = 2;
    foreach ($events as $event) {
        $groupKey = isset($event['group']) ? (string)$event['group'] : '';
        $groupLabel = isset($groups[$groupKey]) ? $groups[$groupKey] : $groupKey;
        $title = isset($event['title']) ? $event['title'] : '';
        $seen = array();
        $historical = isset($event['historical_sources']) && is_array($event['historical_sources']) ? $event['historical_sources'] : array();
        foreach ($historical as $source) {
            $url = isset($source['url']) ? (string)$source['url'] : '';
            $key = (isset($source['year']) ? $source['year'] : '').'|'.$url;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $cells = serviceTimelineXlsxStringCell('A'.$rowNo, $groupLabel, 2)
                .serviceTimelineXlsxStringCell('B'.$rowNo, $title, 3)
                .serviceTimelineXlsxStringCell('C'.$rowNo, isset($source['year']) ? $source['year'] : '', 4)
                .serviceTimelineXlsxStringCell('D'.$rowNo, isset($source['label']) ? $source['label'] : '', 3)
                .serviceTimelineXlsxStringCell('E'.$rowNo, $url, 5);
            $rows[] = '<row r="'.$rowNo.'" ht="30" customHeight="1">'.$cells.'</row>';
            $rowNo++;
        }
        $latestSources = isset($event['sources']) && is_array($event['sources']) ? $event['sources'] : array();
        foreach ($latestSources as $source) {
            $url = isset($source['url']) ? (string)$source['url'] : '';
            $key = 'latest|'.$url;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $cells = serviceTimelineXlsxStringCell('A'.$rowNo, $groupLabel, 2)
                .serviceTimelineXlsxStringCell('B'.$rowNo, $title, 3)
                .serviceTimelineXlsxStringCell('C'.$rowNo, 'Τελευταία διαθέσιμη', 4)
                .serviceTimelineXlsxStringCell('D'.$rowNo, isset($source['label']) ? $source['label'] : '', 3)
                .serviceTimelineXlsxStringCell('E'.$rowNo, $url, 5);
            $rows[] = '<row r="'.$rowNo.'" ht="30" customHeight="1">'.$cells.'</row>';
            $rowNo++;
        }
    }

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        .'<dimension ref="A1:E'.max(1, $rowNo - 1).'"/>'
        .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
        .'<sheetFormatPr defaultRowHeight="18"/>'
        .'<cols>'
          .'<col min="1" max="1" width="18" customWidth="1"/>'
          .'<col min="2" max="2" width="42" customWidth="1"/>'
          .'<col min="3" max="3" width="22" customWidth="1"/>'
          .'<col min="4" max="4" width="62" customWidth="1"/>'
          .'<col min="5" max="5" width="95" customWidth="1"/>'
        .'</cols>'
        .'<sheetData>'.implode('', $rows).'</sheetData><autoFilter ref="A1:E'.max(1, $rowNo - 1).'"/>'
        .'</worksheet>';
}

function serviceTimelineXlsxDosTimeDate()
{
    $t = getdate();
    $year = max(1980, (int)$t['year']);
    $dosTime = (((int)$t['hours'] & 31) << 11) | (((int)$t['minutes'] & 63) << 5) | (((int)$t['seconds'] >> 1) & 31);
    $dosDate = (($year - 1980) << 9) | (((int)$t['mon'] & 15) << 5) | ((int)$t['mday'] & 31);
    return array($dosTime, $dosDate);
}

/** Build a small ZIP archive in pure PHP, used when php-zip/ZipArchive is unavailable. */
function serviceTimelineXlsxZipBinary($files)
{
    list($dosTime, $dosDate) = serviceTimelineXlsxDosTimeDate();
    $local = '';
    $central = '';
    $offset = 0;
    $count = 0;
    foreach ($files as $name => $data) {
        $name = (string)$name;
        $data = (string)$data;
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

function serviceTimelineXlsxBuild($data)
{
    $created = gmdate('Y-m-d\\TH:i:s\\Z');
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>',
        'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:title>Χρονοδιάγραμμα Υπηρεσιακών Μεταβολών</dc:title><dc:creator>Εργαλειοθήκη Εκπαιδευτικού</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">'.$created.'</dcterms:created></cp:coreProperties>',
        'docProps/app.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Εργαλειοθήκη Εκπαιδευτικού</Application></Properties>',
        'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Χρονολόγιο" sheetId="1" r:id="rId1"/><sheet name="Πηγές" sheetId="2" r:id="rId2"/></sheets><calcPr calcId="191029" fullCalcOnLoad="1"/></workbook>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
        'xl/styles.xml' => serviceTimelineXlsxStylesXml(),
        'xl/worksheets/sheet1.xml' => serviceTimelineXlsxChronologySheetXml($data),
        'xl/worksheets/sheet2.xml' => serviceTimelineXlsxSourcesSheetXml($data),
    );

    if (class_exists('ZipArchive')) {
        $tmp = tempnam(sys_get_temp_dir(), 'timelinexlsx_');
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

    return array(true, serviceTimelineXlsxZipBinary($files));
}
