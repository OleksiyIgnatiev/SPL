document.addEventListener('DOMContentLoaded', (event) => {
    // JavaScript для отображения/скрытия всплывающего окна с комментарием
    var commentModal = document.getElementById("commentPopup");
    var inviteBtn = document.getElementById("inviteBtn");
    var applicationId = document.getElementById("applicationId").value; // Получаем applicationId из скрытого поля

    inviteBtn.addEventListener('click', function (event) {
        event.preventDefault();
        commentModal.style.display = "block";
    });

    // Обработчик для кнопки "OK"
    var okBtn = document.getElementById("okInviteBtn");
    okBtn.addEventListener('click', function () {
        var commentInput = document.getElementById("commentInput").value; // Получаем значение из поля ввода комментария

        // Формируем объект данных для отправки на сервер
        var data = {
            application_id: applicationId, // Передаем applicationId
            comment: commentInput
        };

        // Отправляем данные на сервер
        fetch('/controllers/add_invite_api.php', { // Изменяем URL на точку назначения для API добавления приглашения
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
    var cancelBtn = document.getElementById("cancelInviteBtn");
    cancelBtn.addEventListener('click', function () {
        commentModal.style.display = "none";
    });

    window.addEventListener('click', function (event) {
        if (event.target == commentModal) {
            commentModal.style.display = "none";
        }
    });
});
