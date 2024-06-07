document.addEventListener('DOMContentLoaded', (event) => {
    // JavaScript для відображення/приховування вікна форми
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("addVacancyBtn");
    var span = document.getElementsByClassName("close")[0];
    var closeBtn = document.getElementById("closeBtn");
    var submitBtn = document.getElementById('submitBtn');

    btn.addEventListener('click', function (event) {
        event.preventDefault();
        submitBtn.dataset.mode = 'create'
        console.log(modal)
        modal.style.display = "block";
    });

    closeBtn.addEventListener('click',  ()=> {
        modal.style.display = "none";
        console.log('none')

    });

    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

    submitBtn.addEventListener('click', function (event) {
        event.preventDefault();

        var form = document.getElementById('vacancyForm');
        var formData = new FormData(form);
        var data = {};
        var allFilled = true;

        formData.forEach((value, key) => {
            if (!value) {
                allFilled = false;
            }
            if (key === 'is_remote') {
                data[key] = value === 'on' ? 1 : 0;
            } else {
                data[key] = value;
            }
        });

        if (!allFilled) {
            alert('Будь ласка, заповніть всі поля.');
            return;
        }

        console.log('Sending data:', data);  // Виведення даних перед відправленням

        fetch(`/controllers/${submitBtn.dataset.mode == 'create' ? 'add_vacancy_api.php' : 'edit_vacancy_api.php'}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                console.log('Raw response:', response);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.message) {
                    alert(data.message);
                    location.reload();  // Перезавантаження сторінки після успішного додавання
                } else {
                    alert('Unexpected response from server.');
                }
                modal.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error);
            });
    });


    document.querySelectorAll('.deleteVacancyBtn').forEach(button => {
        button.addEventListener('click', function () {
            console.log('1212')
            var vacancyId = this.getAttribute('data-id');
            if (confirm('Ви впевнені, що хочете видалити цю вакансію?')) {
                fetch('/controllers/delete_vacancy_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ vacancy_id: vacancyId })
                })
                    .then(response => {
                        console.log('Raw response:', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.message) {
                            alert(data.message);
                            location.reload();  // Перезавантаження сторінки після успішного видалення
                        } else {
                            alert('Unexpected response from server.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error: ' + error);
                    });
            }
        });
    });


});