function editUser(userId) {
    const row = event.target.closest('tr');
    const username = row.cells[1].innerText;
    const email = row.cells[2].innerText;
    const role = row.cells[3].innerText;

    const newUsername = prompt("Введите новое имя пользователя:", username);
    const newEmail = prompt("Введите новый email:", email);
    const newRole = prompt("Введите новую роль (user или admin):", role);

    if (newUsername && newEmail && newRole) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'manage_users.php';

        const fields = {
            'action': 'edit',
            'user_id': userId,
            'username': newUsername,
            'email': newEmail,
            'role': newRole
        };

        for (const [key, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
}
