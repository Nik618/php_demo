# Демо-сайт PHP для выгрузки данных по обмену валют
Ссылка на гит: https://github.com/Nik618/php_demo/

### Пример работы
В форме выбираем валюты и нажимаем кнопку. Внизу появится таблица с найденными обменниками, их курсами, резервом для выбранной пары валют, а также с указанием числа положительных и отрицательных отзывов:<br><br>
![image](https://user-images.githubusercontent.com/55635768/152244232-7a20a565-39c0-4f19-a43e-4cc2370918ed.png)
<br>В самом низу появится информация о числе найденных обменников и их общем резерве для выбранной пары валют.
<br><br>Также предоставляется возможность рассчитать, сколько одной валюты нужно отдать, чтобы получить заданное количество другой, и наоборот: сколько одной валюты можно получить, если отдать заданное количество другой. Эти операции можно провести в калькуляторе над таблицей. Выбирать между вариантами можно через checkbox справа от поля ввода.
<br><br>Чтобы выбрать обменник, по курсу которого будет производиться расчёт, необходимо нажать на кнопку "select" напротив соответствующей строки таблицы. 

### Общая информация
Код был написан на PHP с использованием HTML/CSS. Для отправки данных из формы использовался метод POST.<br>
Скачанные с сервера файлы находятся в папке files.

### Кэширование
Для организации кэширования была написана функция function cache_get_contents.<br>
Параметр $time (в секундах) - длительность кэша. Функция для кэширования подменяет функцию file_get_contents.<br>
Благодаря кэшированию время ожидания уменьшается.

### Локальный сервер
В качестве локального сервера использовался Open Server.

### Среда разработки
В качестве среды разработки использовалась IDE PhpStorm.

Разработчик: Федейкин Николай
