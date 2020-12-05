<table class="">
    <tr>
        <th>Datasource</th>
        <th>Count</th>
    </tr>
    <?php foreach ($data['data'] as $key=>$value) { ?>
        <tr>
            <th><?php echo $key; ?></th>
            <td><?php echo $value; ?></td>
        </tr>
    <?php } ?>

</table>