<?php

$user = $_SESSION['user'] ?? null;

if (isset($user)) {
    header('Location: /?url=offers');
}

?>

<div class="d-grid" style="grid-template-columns: repeat(2, 1fr); grid-gap: 1rem;">
    <!-- register and auth -->
    <?php if (!isset($_SESSION['user']['id'])): ?>
        <div class="p-4 bg-info-subtle rounded">
            <div class="modal-header">
                <h5 class="modal-title">Регистрация</h5>
            </div>
            <div class="modal-body w-100">
                <form method="post">
                    <input type="hidden" name="form" value="register">
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" class="form-control" name="login" id="login"
                               placeholder="Придумайте логин">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email адрес</label>
                        <input type="email" class="form-control" name="email" id="email"
                               placeholder="Введите Ваш Email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" id="password"
                               placeholder="Придумайте пароль">
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label" for="role">Кто Вы?</label>
                        <select class="form-select" name="role" id="role" aria-label="Default select example">
                            <option value="2">Я рекламодатель</option>
                            <option value="3">Я веб-мастер</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Регистрация</button>
                </form>

                <?php
                if (!empty($_SESSION['checkReg'])) {
                    echo "<div class='snackbar shadow conainer-sm mx-auto mt-5 rounded p-3 border bg-$_SESSION[errors]-subtle font-weight-medium'>";
                    echo $_SESSION['checkReg'];
                    unset($_SESSION['checkReg']);
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="p-4 bg-info-subtle rounded">
            <div class="modal-header">
                <h5 class="modal-title">Авторизация</h5>
            </div>
            <div class="modal-body w-100">
                <form method="post">
                    <input type="hidden" name="form" value="auth">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email адрес</label>
                        <input type="email" class="form-control" name="email" id="email"
                               placeholder="Введите Ваш Email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" id="password"
                               placeholder="Придумайте пароль">
                    </div>
                    <input type="hidden" name="token" value="<? echo $token; ?>">
                    <button type="submit" class="btn btn-primary">Вход</button>
                </form>

                <?php
                if (!empty($_SESSION['checkAuth'])) {
                    echo "<div class='snackbar shadow conainer-sm mx-auto mt-5 rounded p-3 border bg-$_SESSION[errors]-subtle font-weight-medium'>";
                    echo $_SESSION['checkAuth'];
                    unset($_SESSION['checkAuth']);
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- /register and auth -->
</div>

