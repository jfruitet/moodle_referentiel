<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local language pack from http://localhost/m27
 *
 * @package    report
 * @subpackage referentiel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actualisation'] = 'Оновити номери студентів у відповідності з профайлом користувача';
$string['conversionachevee'] = 'Міграція застарілих зв\'язків завершена.';
$string['conversionencours'] = 'Виконується міграція застарілих зв\'язків. Зачекайте, процес може тривати довго. <br />У разі непередбаченого переривання запустіть процес знову.';
$string['etudiants_inscrits_referentiel'] = 'Студенти записані у процеси сертифікації';
$string['migration'] = 'Таблиці модуля Репозиторій компетентностей містять {$a} зв\'язків у форматі Moodle 1.9<br /> Підтвердьте їх конвертування у формат Moodle 2.x';
$string['migrationh'] = 'Міграція застарілих зв\'язків';
$string['migrationh_help'] = 'Під час міграції з Moodle 1.9 на Moodle 2.x деякі зв\'язки таблиць referentiel_document і referentiel_consigne не коректно конвертовані. Ця функція примусово конвертує і переміщує файли старої файлової системи Moodle 1.9 у нову. Ви можете також обрати видалення застарілих файлів (для економії місця на диску).
N.B. Якщо кількість зв\'язків, що підлягають конвертації вимірюється тисячами, то процес конвертації може потребувати години. Тому необхідно адаптувати змінні середовища в файлі php.ini на сервері, щоб збільшити обсяг доступної пам\'яті: 
* max_execution_time = 5000
* memory_limit = 1024M У випадку фатальної помилки \'(Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 284205254 bytes)\' перезапустіть процес щоб завершити конвертацію.';
$string['pluginname'] = 'Репозиторій компетентностей';
$string['profilcheck'] = 'Зміни номера студента, вказаного в профайлах користувачів, буде взято до уваги...';
$string['rank'] = 'Ряд';
$string['suppression'] = 'Підтвердьте видалення застарілих файлів і пошкоджених зв\'язків';
$string['verbose'] = 'Докладний режим (конвертовані посилання відображаються)';
