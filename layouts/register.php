<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <h1>Register</h1>
    <form action="/register/submit" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Register</button>
    </form>

    <?php if (!empty($_GET['error'])): ?>
    <h2>Errors:
    </h2>
    <p>
        <?= $_GET['error'] ?>
    </p>
    <?php endif; ?>
</body>

</html>