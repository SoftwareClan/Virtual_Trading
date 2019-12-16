<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcelFunction
 *
 * @author User
 */
require_once "PHPExcel/PHPExcel.php";
require_once "PHPExcel/PHPExcel/IOFactory.php";

class ExcelFunction {

    function excel_fetch_data($path, $header_array, $data_type_array, $compulsary_column) {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(TRUE);
        $excel = $objReader->load($path);
        $worksheet_instance = $excel->setActiveSheetIndex();
        return $this->excel_validation_data($header_array, $data_type_array, $compulsary_column, $worksheet_instance);
    }

    /*
     * @params $column array => containe column name $apply_to_no_row => how many row will be formatted, $filename=> file name start with and container current data.
     * @return excel file as output
     */

    public function create_excel($columnArray, $apply_to_no_rom, $filename) {
        //Creating a new workbook
        $objPHPExcel = new PHPExcel();
        //Adding a new Worksheet
        $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Stock Watch');
        $objPHPExcel->addSheet($myWorkSheet, 0);
        // column name user_name mobile_no state city email address country
        foreach ($columnArray as $index => $column) {
            if (array_key_exists("name", $column)) {
                $myWorkSheet->getCellByColumnAndRow($index, 1)->setValue($column["name"]);
                $myWorkSheet->getColumnDimensionByColumn($index)
                        ->setAutoSize(true);
                $myWorkSheet->getStyleByColumnAndRow($index, 1)->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE8E5E5');
                $myWorkSheet->getStyleByColumnAndRow($index, 1)->getFont()->setBold(true);
            }

            for ($i = 2; $i < $apply_to_no_rom; $i++) {
                $objValidation = $myWorkSheet->getCellByColumnAndRow($index, $i)->getDataValidation();

                if (array_key_exists("data_validation_type", $column)) {
                    $objValidation->setType($column["data_validation_type"]);
                }
                if (array_key_exists("error_style", $column)) {
                    $objValidation->setErrorStyle($column["error_style"]);
                }
                if (array_key_exists("data_type", $column)) {
                    switch ($column["data_type"]) {
                        case 1:$myWorkSheet->getCellByColumnAndRow($index, $i)->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
                            break;
                        case 2:$myWorkSheet->getCellByColumnAndRow($index, $i)->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                            break;
                        case 3:$myWorkSheet->getCellByColumnAndRow($index, $i)->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                            break;
                    }
                }
                if (array_key_exists("allow_blank", $column)) {
                    $objValidation->setAllowBlank($column["allow_blank"]);
                }
                if (array_key_exists("is_input_message", $column)) {
                    $objValidation->setShowInputMessage($column["is_input_message"]);
                }
                if (array_key_exists("is_error_message", $column)) {
                    $objValidation->setShowErrorMessage($column["is_error_message"]);
                }
                if (array_key_exists("drop_down", $column)) {
                    $objValidation->setShowDropDown($column["drop_down"]);
                }
                if (array_key_exists("error_title", $column)) {
                    $objValidation->setErrorTitle($column["error_title"]);
                }
                if (array_key_exists("error_message", $column)) {
                    $objValidation->setError($column["error_message"]);
                }
                if (array_key_exists("promt_title", $column)) {
                    $objValidation->setPromptTitle($column["promt_title"]);
                }
                if (array_key_exists("promt_message", $column)) {
                    $objValidation->setPrompt($column["promt_message"]);
                }
                if (array_key_exists("set_formula", $column)) {
                    $str = implode(",", $column["set_formula"]);
                    $objValidation->setFormula1('"' . $str . '"');
                }
            }
        }

        // header setting
        $date = date("d-m-y");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"' . $date . '.xlsx"');
        header('Cache-Control: max-age=0');
        $file = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $file->save('php://output');
    }

    /*
     * @params $header_array=> all column name , $data_type_arrray=>all column data type , $worksheet _instance => active worksheet instance required to read data
     * @return error if there is not  proper sequeance and data type as you specify  other wise return excel data in array formate.
     */

    function excel_validation_data($header_array, $data_type_array, $compulsary_column, $worksheet_instance) {
        foreach ($worksheet_instance->getRowIterator(1) as $row => $rowInstance1) {
            $cellIterator1 = $rowInstance1->getCellIterator();
            $cellIterator1->setIterateOnlyExistingCells(FALSE);
            $index = 0;
            foreach ($cellIterator1 as $columInstance1) {
//                echo $header_array[$index], "|", $columInstance1->getValue(), var_dump($header_array[$index] !== $columInstance1->getValue());
                if ($header_array[$index] !== $columInstance1->getValue()) {
                    return "Excel Column Formate was Changed.";
                }
                $index++;
            }
            break;
        }



        $highestColumn = $worksheet_instance->getHighestColumn();
        $highestRow = $worksheet_instance->getHighestRow();
        $sheet_data = array();

        foreach ($worksheet_instance->getRowIterator(2) as $row => $rowInstance) {
            $data = array();
            $cellIterator = $rowInstance->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $column = 0;
            foreach ($cellIterator as $columInstance) {

                if (in_array("number", $data_type_array)) {
                    if (in_array($column, $data_type_array["number"])) {
                        if (!is_numeric($columInstance->getValue())) {
                            return "Error at Column no " . $column . " and row no " . $row . " invalid data required number formated data";
                        }
                    }
                }

                if (in_array("string", $data_type_array)) {
                    if (in_array($column, $data_type_array["string"])) {
                        if (!is_string($columInstance->getValue())) {
                            return "Error at Column no " . $column . " and row no " . $row . " invalid data required String formated data";
                        }
                    }
                }

                if (in_array("datetime", $data_type_array)) {
                    if (in_array($column, $data_type_array["datetime"])) {
                        if (is_numeric($columInstance->getValue())) {
                            $UNIX_DATE = ( $columInstance->getValue() - 25569) * 86400;
                            $date = gmdate("d-m-Y", $UNIX_DATE);
                        } else {
                            $date = date(PHPExcel_Style_NumberFormat::toFormattedString($columInstance->getValue(), 'DD-MM-YYYY'));
                        }
                        if ($date) {
                            return "Error at Column no " . $column . " and row no " . $row . " invalid data required datetime formated data";
                        }
                    }
                }

                if (is_array($compulsary_column)) {
                    if (in_array($column, $compulsary_column)) {
                        if (is_null($columInstance->getValue())) {
                            break;
                        } else {
                            if (is_numeric($columInstance->getValue())) {
                                $UNIX_DATE = ( $columInstance->getValue() - 25569) * 86400;
                                $date = gmdate("d-m-Y", $UNIX_DATE);
                            } else {
                                $date = "";
                            }
                            if ($date != "") {
                                $data[$header_array[$column]] = $columInstance->getValue();
                            } else {
                                $data[$header_array[$column]] = PHPExcel_Style_NumberFormat::toFormattedString($columInstance->getValue(), 'DD-MM-YYYY');
                            }
                        }
                    } else {
                        if (is_numeric($columInstance->getValue())) {
                            $UNIX_DATE = ( $columInstance->getValue() - 25569) * 86400;
                            $date = gmdate("d-m-Y", $UNIX_DATE);
                        } else {
                            $date = "";
                        }
                        if ($date != "") {
                            $data[$header_array[$column]] = $columInstance->getValue();
                        } else {
                            $data[$header_array[$column]] = PHPExcel_Style_NumberFormat::toFormattedString($columInstance->getValue(), 'DD-MM-YYYY');
                        }
                    }
                } else {
                    if (is_null($columInstance->getValue())) {
                        break;
                    } else {
                        switch ($columInstance->getValue()) {
                            case is_numeric($columInstance->getValue()):
                                if (in_array($column, $data_type_array["datetime"])) {
                                    $UNIX_DATE = ( $columInstance->getValue() - 25569) * 86400;
                                    $date = gmdate("d-m-Y", $UNIX_DATE);
                                    $data[$header_array[$column]] = $date;
                                } else {
                                    $data[$header_array[$column]] = $columInstance->getValue();
                                }
                                break;
                            case is_string($columInstance->getValue()):
                                if (in_array($column, $data_type_array["datetime"])) {
                                    if ($columInstance->getValue() != "NA") {
                                        $data[$header_array[$column]] = PHPExcel_Style_NumberFormat::toFormattedString($columInstance->getValue(), 'DD-MM-YYYY');
                                    } else {
                                        $data[$header_array[$column]] = "0000-00-00 00:00:00";
                                    }
                                } else {
                                    if ($columInstance->getValue() != "NA") {
                                        $data[$header_array[$column]] = $columInstance->getValue();
                                    } else {
                                        $data[$header_array[$column]] = "0000-00-00 00:00:00";
                                    }
                                }
                                break;
                            default:
                                $data[$header_array[$column]] = $columInstance->getValue();
                        }
                    }
                }


                $column++;
            }
            if (count($data) > 0) {
                array_push($sheet_data, $data);
            }
        }

        return $sheet_data;
    }

}
