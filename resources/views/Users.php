<table class="table">
    <thead>
    <tr>
        <th scope="col">Id</th>
        <th scope="col">Login</th>
        <th scope="col">Email</th>
        <th scope="col">Role</th>
        <th scope="col">Role ID</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (empty($data)) {
        echo "<tr>";
        echo "<td colspan='4' class='text-center py-4'> В базе данных нет ни одного пользователя. </td>";
        echo "</tr>";
    } else {
        foreach ($data as $key => $val) {
            echo "<tr>";
            echo "<th scope='row'>" . $val['id'] . "</th>";
            echo "<td>" . $val['name'] . "</td>";
            echo "<td>" . $val['email'] . "</td>";
            echo "<td>" . $val['role'] . "</td>";
            echo "<td>" . $val['role_id'] . "</td>";
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>
