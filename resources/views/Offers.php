<?php

$user = $_SESSION['user'];
$user_id = $user['id'];
$offers = $data['offers'] ?? [];

if (!isset($user_id)) {
    header('Location: /');
}

?>

<h3 class="card-title">
    <?php
    echo ucfirst($user['name']) . ", добро пожаловать на страницу предложений!";
    ?>
</h3>

<h6 class="card-subtitle mb-3 text-body-secondary">
    <span class="opacity-25">(<?= $user['role']; ?>)</span>
    <?php if (isset($user) && $user['role_id'] == '2'): ?>
        Здесь вы можете создать своё предложение.
    <?php elseif ($user['role_id'] == '3'): ?>
        Здесь вы можете переходить по созданным предложениям.
    <?php endif; ?>
</h6>

<!-- dialog create offer -->
<!-- button trigger modal -->
<div class="d-flex w-100 justify-content-between align-items-center">
    <div class="col-9">
        <h5>
            <?php
            if ($user['role_id'] == '2') {
                echo 'Мои предложения';
            } else {
                echo 'Доступные предложения';
            }

            echo ' (' . count($offers) . ')' ?? ' (0)'
            ?>
        </h5>
    </div>
    <div class="col-3 d-flex justify-content-end">
        <?php if (isset($user) && $user['role_id'] == '2'): ?>
            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Создать новое
            </button>
        <?php endif; ?>
    </div>
</div>
<!-- /button trigger modal -->

<!-- modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Создать предложение</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- form offer -->
                <form method="post">
                    <input type="hidden" id="form" name="form" value="create_offer">
                    <div class="mb-3">
                        <label for="title" class="col-sm-12 col-form-label">Название</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="payment" class="col-sm-12 col-form-label">Стоимость перехода</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="payment" name="payment">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="path" class="col-sm-12 col-form-label">Целевой URL</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="path" name="path">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="theme" class="col-sm-12 col-form-label">Тема</label>
                        <div class="col-sm-12">
                            <select class="form-select" name="theme" id="theme" aria-label="Default select example">
                                <option value="1">theme 1</option>
                                <option value="2">theme 2</option>
                                <option value="3">theme 3</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Создать</button>
                </form>
                <!-- /form offer -->
            </div>
        </div>
    </div>
</div>
<!-- /modal -->
<!-- /dialog create offer -->

<div class="row">
    <?php if (!count($offers)): ?>
        <div class="col-12">
            <p>Нет доступных предложений</p>
        </div>
    <?php else: ?>
        <?php foreach ($offers as $key => $val): ?>
            <?php if (isset($val['id'])): ?>
                <div class="col-4 mb-4">
                    <div class="card card-form_send">
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between">
                                <?= $val['title']; ?>

                                <?php if (isset($user) && $user['role_id'] == '3'): ?>
                                    <form method="post">
                                        <input type="hidden" name="form" value="following">
                                        <input type="hidden" name="offer_id" value="<?php echo $val['id']; ?>">
                                        <input type="hidden" name="author_id" value="<?php echo $val['creator_id']; ?>">
                                        <input type="hidden" name="follower_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-light">
                                            <?php if ($val['following'] == 1): ?>
                                                <svg width="24" height="24" fill="orange"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 24 24">
                                                    <title>Unfollow</title>
                                                    <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                                </svg>
                                            <?php else: ?>
                                                <span class="opacity-50">
                                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 24 24">
                                                    <title>Follow</title>
                                                    <path d="M12,15.39L8.24,17.66L9.23,13.38L5.91,10.5L10.29,10.13L12,6.09L13.71,10.13L18.09,10.5L14.77,13.38L15.76,17.66M22,9.24L14.81,8.63L12,2L9.19,8.63L2,9.24L7.45,13.97L5.82,21L12,17.27L18.18,21L16.54,13.97L22,9.24Z"/>
                                                </svg>
                                            </span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </h4>

                            <h6 class="card-subtitle mb-2 text-body-secondary">
                                От <?php
                                $date = date_create($val['created']);
                                echo date_format($date, 'd.m.Y');
                                ?>
                            </h6>

                            <p>
                                Тематика: <?= $val['theme']; ?>
                            </p>

                            <?php if (isset($user) && ($val['creator_id'] == $user_id || $user['role_id'] == '1')): ?>
                                <details>
                                    <summary>Детали</summary>
                                    <div class="p-2 bg-light">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>
                                                → Число подписчиков</span>
                                            <?= $val['followers']; ?>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>
                                                → Переходы (клики)</span>
                                            <?= $val['transitions']; ?>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>→ Расходы</span>
                                            <?= $val['total_cost'] . ' руб.'; ?>
                                        </div>
                                        <div class="text-nowrap mb-1 overflow-hidden w-100 text-truncate">
                                            → <?= $val['url']; ?>
                                        </div>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center text-danger">
                                <div class="d-flex justify-content-between">
                                    <span><?= $val['payment'] . ' руб.'; ?></span>
                                    <?php if (isset($user) && $user_id == $val['user_id'] && $user['role_id'] == '3'): ?>
                                        <span>&nbsp;| Доход:&nbsp;</span>
                                        <?php
                                        if(!empty($val['master_amount'])) {
                                            echo $val['master_amount'] . ' руб.';
                                        } else {
                                            echo '0 руб.';
                                        } ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($user) && $user['role_id'] == '3'): ?>
                                    <form method="post" class="form_send">
                                        <input type="hidden" name="form" value="form_send">
                                        <input type="hidden" name="send_id" value="<?php echo $val['id']; ?>"
                                               class="form_send-id">
                                        <input type="hidden" name="send_payment" value="<?php echo $val['payment']; ?>"
                                               class="form_send-payment">
                                        <input type="hidden" name="send_transition"
                                               value="<?php echo $val['transitions']; ?>" class="form_send-transitions">
                                        <input type="hidden" name="send_user_id" value="<?php echo $user['id']; ?>"
                                               class="form_send-user_id">
                                        <button type="submit"
                                                class="form_send-btn w-100 btn btn-primary form_send-link">
                                            Перейти
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($val['creator_id'] == $user_id || $user['role_id'] == '1'): ?>
                                    <?php if ($val['state'] == '1'): ?>
                                        <form method="post">
                                            <input type="hidden" id="form" name="form" value="inactive_offer">
                                            <input name="val_id" id="val_id" type="hidden"
                                                   value="<?php echo $val['id']; ?>">
                                            <input name="set_state" id="set_state" type="hidden" value="0">
                                            <button type="submit" class="btn btn-outline-secondary">Деактивировать
                                            </button>
                                        </form>

                                    <?php elseif ($val['state'] == '0'): ?>
                                        <form method="post">
                                            <input type="hidden" id="form" name="form" value="active_offer">
                                            <input name="val_id" id="val_id" type="hidden"
                                                   value="<?php echo $val['id']; ?>">
                                            <input name="set_state" id="set_state" type="hidden" value="1">
                                            <button type="submit" class="btn btn-outline-secondary">Активировать
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
