document.addEventListener('DOMContentLoaded', (event) => {
    // JavaScript для отображения/скрытия всплывающего окна с комментарием
    var commentModal = document.getElementById("commentPopup");
    var openPopupBtn = document.getElementById("openPopupBtn");
    var vacancyId = document.getElementById("vacancyId").value;

    openPopupBtn.addEventListener('click', function (event) {
        event.preventDefault();
        commentModal.style.display = "block";
    });

    // Обработчик для кнопки "OK"
    var okBtn = document.getElementById("okBtn");
    okBtn.addEventListener('click', function () {
        var commentInput = document.getElementById("commentInput").value; // Получаем значение из поля ввода комментария

        // Формируем объект данных для отправки на сервер
        var data = {
            vacancy_id: vacancyId, // Передаем vacancy_id
            description: commentInput
            // Добавьте остальные данные, если необходимо
        };

        // Отправляем данные на сервер
        fetch('/controllers/add_application_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response data:', data);
            if (data.message) {
                alert(data.message);
                location.reload();  // Перезагружаем страницу после успешного добавления
            } else {
                alert('Unexpected response from server.');
            }
            commentModal.style.display = 'none'; // Скрываем модальное окно
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error);
            commentModal.style.display = 'none'; // Скрываем модальное окно в случае ошибки
        });
    });

    // Обработчик для кнопки "Отмена"
    var cancelBtn = document.getElementById("cancelBtn");
    cancelBtn.addEventListener('click', function () {
        commentModal.style.display = "none";
    });

    window.addEventListener('click', function (event) {
        if (event.target == commentModal) {
            commentModal.style.display = "none";
        }
    });
});
