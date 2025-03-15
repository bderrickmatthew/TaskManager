<nav>
    <a href="/">Home</a>
    |
    <a href="/tasks">Tasks</a>
    |
    <?php if (isset($_SESSION['userLogged']) && $_SESSION['userLogged']): ?>
        <a href="/logout">Logout</a>
    <?php else: ?>
        <a href="/login">Login</a>
        |
        <a href="/register">Register</a>
        <!-- Add this line -->
    <?php endif; ?>
</nav>

