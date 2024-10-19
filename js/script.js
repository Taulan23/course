function showUploadForm() {
    document.getElementById('uploadForm').style.display = 'block';
}

document.getElementById('documentUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var fileInput = document.getElementById('document');
    var file = fileInput.files[0];
    
    if (file.size > 50 * 1024 * 1024) { // 50 MB в байтах
        alert('Файл слишком большой. Максимальный размер 50 МБ.');
        return;
    }
    
    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(result => {
        console.log(result);
        if (result.error) {
            alert('Error: ' + result.error);
        } else if (result.success) {
            alert('Success: ' + result.success);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the file: ' + error.message);
    });
});

function viewDocument(id) {
    window.location.href = 'view_document.php?id=' + id;
}

function downloadDocument(id) {
    window.location.href = 'download_document.php?id=' + id;
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch('delete_document.php?id=' + id, { method: 'POST' })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + result.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
    }
}

function посмотреть() {
    fetch('../scripts/проверка_лимитов_загрузки.php')
        .then(response => response.json())
        .then(data => {
            alert('Результаты проверки лимитов загрузки:\n' + JSON.stringify(data, null, 2));
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при получении данных');
        });
}

function addComment(id) {
    const comment = prompt("Введите ваш комментарий:");
    if (comment) {
        fetch('add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `document_id=${id}&comment=${encodeURIComponent(comment)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Комментарий успешно добавлен');
            } else {
                alert('Ошибка при добавлении комментария: ' + result.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при добавлении комментария');
        });
    }
}

function shareDocument(id) {
    const username = prompt("Введите имя пользователя, с которым хотите поделиться документом:");
    if (username) {
        fetch('share_document.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `document_id=${id}&username=${encodeURIComponent(username)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Документ успешно отправлен пользователю ' + username);
            } else {
                alert('Ошибка при отправке документа: ' + result.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при отправке документа');
        });
    }
}
