<?php

namespace App\Services;

class StatisticsService
{
    public function candidates($Candidates)
    {
        $columns_table = [
            'total' => 'Всего',
            //in work
            // '2' => 'Лид',            
            // '1' => 'Новый кандидат',
            '14' => 'Перезвонить',
            'in_work' => 'Новый кандидат',
            //oforml
            '4' => 'Оформлен',
            '3' => 'Отказ',
            'c_3' => 'Конверсия отказы',
            //logist
            '6' => 'Подтвердил Выезд',
            // '21' => 'Перезвонить',
            'logist_3' => 'Отказ',
            '19' => 'В пути',             
            //trud           
            '12' => 'Приехал',
            'c_12' => 'Конверсия приехавших',
            '20' => 'Не доехал',
            'trud_3' => 'Отказ',
            '22' => 'Не рекрутируем',
            '8' => 'Трудоустроен',
            //Координатор                             
            '7' => 'Заселен',
            '9' => 'Приступил к Работе',
            'worked' => 'Отработал 7 дней',
            'c_worked' => 'Конверсия отработал<br> 7 дней',
            '11' => 'Уволен',
            //Общие
            '5' => 'Архив',
        ];

        $table = [];

        foreach ($columns_table as $key => $v) {
            $table[$key] = 0;
        }

        if ($Candidates) {
            foreach ($Candidates as $Candidate) {

                $is_worked = false;

                $is_11 = false;
                $is_9 = false;
                $is_7 = false;
                $is_8 = false;
                $is_22 = false;
                $is_12 = false;
                $is_20 = false;
                $is_19 = false;
                $is_6 = false;
                $is_21 = false;
                $is_4 = false;
                $is_3 = false;
                $is_trud_3 = false;
                $is_logist_3 = false;
                $is_5 = false;
                $is_14 = false;
                $is_in_work = false;

                $is_also_11 = false;
                $is_also_9 = false;
                $is_also_7 = false;
                $is_also_8 = false;
                $is_also_22 = false;
                $is_also_12 = false;
                $is_also_20 = false;
                $is_also_19 = false;
                $is_also_6 = false;
                $is_also_21 = false;
                $is_also_4 = false;
                $is_also_trud_3 = false;
                $is_also_logist_3 = false;
                $is_also_3 = false;
                $is_also_5 = false;

                if ($Candidate->ActiveHistory && count($Candidate->ActiveHistory) > 0) {
                    $table['total']++;

                    if ($Candidate->worked) {
                        $is_worked = true;
                    }

                    $i = 0;
                    foreach ($Candidate->ActiveHistory as $item) {
                        if ($i == 0){
                            if ($item->current_value == '11') {
                                $is_11 = true;
                            }

                            if ($item->current_value == '9') {
                                $is_9 = true;
                            }

                            if ($item->current_value == '7') {
                                $is_7 = true;
                            }

                            if ($item->current_value == '8') {
                                $is_8 = true;
                            }

                            if ($item->current_value == '22') {
                                $is_22 = true;
                            }

                            if ($item->current_value == '12') {
                                $is_12 = true;
                            }

                            if ($item->current_value == '20') {
                                $is_20 = true;
                            }

                            if ($item->current_value == '19') {
                                $is_19 = true;
                            }

                            if ($item->current_value == '6') {
                                $is_6 = true;
                            }

                            if ($item->current_value == '21') {
                                $is_21 = true;
                            }

                            if ($item->current_value == '4') {
                                $is_4 = true;
                            }

                            if (
                                $item->current_value == '1' 
                                || $item->current_value == '2'
                                || $item->current_value == '14'
                            ) {
                                $is_in_work = true;
                            }

                            if ($item->current_value == '14') {
                                $is_14 = true;
                            }

                            if ($item->current_value == '5') {
                                $is_5 = true;
                            }

                            if ($item->current_value == '3') {
                                if ($item->user_role == 5) {
                                    $is_trud_3 = true;
                                } elseif ($item->user_role == 4) {
                                    $is_logist_3 = true;
                                } else {
                                    $is_3 = true;
                                }
                            }

                        } else {
                            if ($item->current_value == '11') {
                                $is_also_11 = true;
                            }

                            if ($item->current_value == '9') {
                                $is_also_9 = true;
                            }

                            if ($item->current_value == '7') {
                                $is_also_7 = true;
                            }

                            if ($item->current_value == '8') {
                                $is_also_8 = true;
                            }

                            if ($item->current_value == '22') {
                                $is_also_22 = true;
                            }

                            if ($item->current_value == '12') {
                                $is_also_12 = true;
                            }

                            if ($item->current_value == '20') {
                                $is_also_20 = true;
                            }

                            if ($item->current_value == '19') {
                                $is_also_19 = true;
                            }

                            if ($item->current_value == '6') {
                                $is_also_6 = true;
                            }

                            if ($item->current_value == '21') {
                                $is_also_21 = true;
                            }

                            if ($item->current_value == '4') {
                                $is_also_4 = true;
                            }

                            if ($item->current_value == '3') {
                                if ($item->user_role == 5) {
                                    $is_also_trud_3 = true;
                                } elseif ($item->user_role == 4) {
                                    $is_also_logist_3 = true;
                                } else {
                                    $is_also_3 = true;
                                }
                            }

                            if ($item->current_value == '5') {
                                $is_also_5 = false;
                            }
                        }

                        $i++;
                    }
                }
                    
                if ($is_5) {
                    $table['5']++;

                    if ($is_also_trud_3) {
                        $table['trud_3']++;
                    } elseif ($is_also_logist_3) {
                        $table['logist_3']++;
                    } elseif ($is_also_3) {
                        $table['3']++;
                    }

                    if ($is_also_11) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_also_9) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_also_7) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_also_8) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_also_22) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_also_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    if ($is_also_21) {
                        // $table['21']++;
                        $table['4']++;
                        if ($is_also_6) {
                            $table['6']++;
                        }
                        continue;
                    }
                    
                    if ($is_also_4) {
                        $table['4']++;
                        continue;
                    }

                    continue;
                }

                if ($is_trud_3 || $is_logist_3 || $is_3) {

                    if ($is_trud_3) {
                        $table['trud_3']++;
                    } elseif ($is_logist_3) {
                        $table['logist_3']++;
                    } elseif ($is_3) {
                        $table['3']++;
                    }

                    if ($is_also_11) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_also_9) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_also_7) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_also_8) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_also_22) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_also_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    if ($is_also_21) {
                        // $table['21']++;
                        $table['4']++;
                        if ($is_also_6) {
                            $table['6']++;
                        }
                        continue;
                    }
                    
                    if ($is_also_4) {
                        $table['4']++;
                        continue;
                    }

                    continue;
                }

                if ($is_11) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['8']++;
                    $table['7']++;
                    $table['9']++;
                    $table['11']++;

                    if ($is_worked) {
                        $table['worked']++;
                    }

                    continue;
                }

                if ($is_worked) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['8']++;
                    $table['7']++;
                    $table['9']++;
                    $table['worked']++;
                    continue;
                }

                if ($is_9) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['8']++;
                    $table['7']++;
                    $table['9']++;
                    continue;
                }

                if ($is_7) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['8']++;
                    $table['7']++;
                    continue;
                }

                if ($is_8) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['8']++;
                    continue;
                }

                if ($is_22) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    $table['22']++;
                    continue;
                }

                if ($is_12) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['12']++;
                    continue;
                }

                if ($is_20) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;
                    $table['20']++;
                    continue;
                }

                if ($is_19) {
                    $table['4']++;
                    $table['6']++;
                    $table['19']++;

                    if ($is_also_12) {
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['20']++;
                        continue;
                    }

                    continue;
                }

                if ($is_6) {
                    $table['4']++;
                    $table['6']++;
                    continue;
                }

                if ($is_21) {
                    if ($is_also_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    continue;
                }
                
                if ($is_4) {
                    $table['4']++;

                    if ($is_also_5) {
                        $table['5']++;
                    }

                    if ($is_also_11) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_also_9) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_also_7) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_also_8) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_also_22) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_also_12) {
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['6']++;
                        continue;
                    }

                    if ($is_also_21) {
                        if ($is_also_6) {
                            $table['6']++;
                        }
                        continue;
                    }

                    continue;
                }

                if ($is_14) {
                    $table['14']++;

                    if ($is_also_5) {
                        $table['5']++;
                    }

                    if ($is_also_trud_3) {
                        $table['trud_3']++;
                    } elseif ($is_also_3) {
                        $table['3']++;
                    }

                    if ($is_also_11) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_also_9) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_also_7) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_also_8) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_also_22) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_also_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    if ($is_also_21) {
                        $table['4']++;
                        if ($is_also_6) {
                            $table['6']++;
                        }
                        continue;
                    }
                    
                    if ($is_also_4) {
                        $table['4']++;
                        continue;
                    }
                    
                    continue;
                }

                if ($is_in_work) {
                    $table['in_work']++;

                    if ($is_also_5) {
                        $table['5']++;
                    }

                    if ($is_also_trud_3) {
                        $table['trud_3']++;
                    } elseif ($is_also_logist_3) {
                        $table['logist_3']++;
                    } elseif ($is_also_3) {
                        $table['3']++;
                    }

                    if ($is_also_11) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_also_9) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_also_7) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_also_8) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_also_22) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_also_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_also_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_also_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        continue;
                    }

                    if ($is_also_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    if ($is_also_21) {
                        $table['4']++;
                        if ($is_also_6) {
                            $table['6']++;
                        }
                        continue;
                    }
                    
                    if ($is_also_4) {
                        $table['4']++;
                        continue;
                    }
                    
                    continue;
                }
            }
        }

        $table['c_3'] = $table['total'] ? round(($table['3'] / $table['total']) * 100) : 0;
        $table['c_12'] = $table['6'] ? round(($table['12'] / $table['6']) * 100) : 0;
        $table['c_worked'] = $table['8'] ? round(($table['worked'] / $table['8']) * 100) : 0;

        return $table;
    }
}