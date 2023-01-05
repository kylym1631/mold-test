<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldsMutation extends Model
{
    use HasFactory;

    public static function getRolesArr()
    {
        return array(
            "1" => "Администратор",
            "2" => "Рекрутер",
            "3" => "Фрилансер",
            "4" => "Логист",
            "5" => "Трудоустройство",
            "6" => "Координатор",
            "7" => "Бухгалтер",
            "8" => "Менеджер поддержки",
            "9" => 'Директор отдела рекрутации',
            "10" => 'Аудитор звонков',
            "11" => 'Маркетолог',
        );
    }

    public static function getRoleTitle($key)
    {
        $dictr = self::getRolesArr();
        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }

    public static function getFieldTitle($key)
    {
        $dictr = array(
            "firstName" => "Имя",
            "lastName" => "Фамилия",
            "dateOfBirth" => "Дата рождения",
            "phone" => "Телефон",
            "viber" => "Номер Viber",
            "phone_parent" => "Дополнительный контакт",
            "citizenship_id" => "Гражданство",
            "nacionality_id" => "Национальность",
            "country_id" => "Страна прибывания",
            "date_arrive" => "Дата и время приезда",
            "type_doc_id" => "Документ",
            "transport_id" => "Транспорт",
            "comment" => "Комментарий",
            "inn" => "ИНН",
            "reason_reject" => "Причина отказа",
            "is_payed" => "",
            "cost_pay" => "",
            "cost_pay_lead" => "",
            "client_id" => "Клиент",
            "logist_date_arrive" => "Дата и время приезда",
            "date_start_work" => "",
            "logist_place_arrive_id" => "Место приезда",
            "place_arrive_id" => "Место приезда",
            "real_vacancy_id" => "Вакансия",
            "real_status_work_id" => "Статус трудоустройства",
            "recruiter_id" => "Рекрутер",
            "active" => "Статус",
            "status" => "Статус",
            "file_type_3" => "Паспорт(ID card)",
            "file_type_103" => "Карта по быту(вместе с децизией)",
            "file_type_104" => "Водительское удостоверение",
            "file_type_105" => "Диплом(сертификаты)",
            "file_type_106" => "Легитимация из Универа",
            "file_type_107" => "Прочий документ",
            "housing_id" => "Жилье",
            "housing_room_id" => "Комната",
            "residence_started_at" => "Дата начала проживания",
            "residence_stopped_at" => "Дата выселения",
            "gender" => "Пол",
            "own_housing" => "Свое жилье",
            "client_position_id" => "Должность",
            "pesel" => "PESEL",
            "account_number" => "Номер банковского счета",
            "mothers_name" => "Имя матери",
            "fathers_name" => "Имя отца",
            "address" => "Адрес",
            "zip" => "Индекс",
            "city" => "Город",
        );

        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }

    public static function getLeadFieldTitle($key)
    {
        $dictr = array(
            "status" => "Статус",
            "status_comment" => "Комментарий",
        );

        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }

    public static function getStatusTitle($key)
    {
        $dictr = array(
            '1' => 'Новый кандидат',
            '2' => 'Лид',
            '3' => 'Отказ',
            '4' => 'Оформлен',
            '5' => 'Архив',
            '6' => 'Подтвердил Выезд',
            '7' => 'Заселен',
            '8' => 'Трудоустроен',
            '9' => 'Приступил к Работе',
            '10' => 'Проверка легализации',
            '11' => 'Уволен',
            '12' => 'Приехал',
            '13' => 'Архив (отказ)',
            '14' => 'Перезвонить',
            '15' => 'Недозвон',
            '16' => 'Оформление',
            '17' => 'Жду документа',
            '18' => 'Не говорит по русски',
            '19' => 'В пути',
            '20' => 'Не доехал',
            '21' => 'Перезвонить',
            '22' => 'Не рекрутируем',
        );

        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }

    public static function getLeadStatusTitle($key)
    {
        $dictr = array(
            '0' => 'Новый лид',
            '1' => 'Горячий',
            '2' => 'Не оставлял заявку',
            '3' => 'Не дозвон',
            '4' => 'Перезвонить',
            '5' => 'Брак номера',
            '6' => 'Не рекрутируем',
            '7' => 'Не заинтересован',
        );

        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }

    public static function getArrivalStatusTitle($key)
    {
        $dictr = array(
            "0" => "Оформлен",
            "1" => "В пути",
            "2" => "Приехал",
            "3" => "Не доехал",
        );

        return isset($dictr[$key]) ? $dictr[$key] : $key;
    }
}
