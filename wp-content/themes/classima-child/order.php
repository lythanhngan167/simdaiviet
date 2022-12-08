<?php
function donhang() {
    $array = array(
        '1'=>array(
            'name' => 'Trương thị Thủy',
            'phone' => '0951679xxx',
            'status' => 'Đang giao hàng',
            'time' => '(08h44:18)'
        ),
        '2'=>array(
            'name' => 'Trương Văn Vân',
            'phone' => '0962381xxx',
            'status' => 'Chờ khách chuyển khoản',
            'time' => '(08h41:55)'
        ),
        '3'=>array(
            'name' => 'Đặng Khánh Thảo',
            'phone' => '0999396xxx',
            'status' => 'Chờ khách chuyển khoản',
            'time' => '(08h34:08)'
        ),
        '4'=>array(
            'name' => 'Ngô Hoàng Nhi',
            'phone' => '0996224xxx',
            'status' => 'Chờ khách chuyển khoản',
            'time' => '(08h31:24)'
        ),
        '5'=>array(
            'name' => 'Nguyễn Mạnh Cường',
            'phone' => '0953639xxx',
            'status' => 'Đang giao hàng',
            'time' => '(08h44:18)'
        ),
        '6'=>array(
            'name' => 'Nguyễn Hoàng Long',
            'phone' => '0937961xxx',
            'status' => 'Đang chuyển COD',
            'time' => '(21h00:53)'
        ),
        '7'=>array(
            'name' => 'Bùi Hoàng Anh',
            'phone' => '0944574xxx',
            'status' => 'Chờ khách chuyển khoản',
            'time' => '(23h10:44)'
        ),
        '8'=>array(
            'name' => 'Lê Khánh Long',
            'phone' => '0923558xxx',
            'status' => 'Đang giao hàng',
            'time' => '(23h07:46)'
        ),
        '9'=>array(
            'name' => 'Nguyễn Hoài Văn',
            'phone' => '0931888xxx',
            'status' => 'Đang chuyển COD',
            'time' => '(23h01:50)'
        ),
        '10'=>array(
            'name' => 'Ngô Hoàng Tú',
            'phone' => '0965882xxx',
            'status' => 'Chờ khách đến lấy',
            'time' => '(22h57:36)'
        ),
        '11'=>array(
            'name' => 'Bùi Tuấn Anh',
            'phone' => '0943281xxx',
            'status' => 'Chờ khách chuyển khoản',
            'time' => '(22h54:58)'
        ),
        '12'=>array(
            'name' => 'Đỗ Nam Tú',
            'phone' => '0987637xxx',
            'status' => 'Đang chuyển COD',
            'time' => '(23h00:18)'
        ),
    );
shuffle($array);
    ?>
    <div class="panel-1">
        <div class="panel-header">ĐƠN HÀNG ĐANG XỬ LÝ</div>
        <div class="panel-content">
            <div class="">
                <ul class="list-order">
                    <?php for($i=0;$i<5;$i++){ ?>
                    <li>
                        <p class="order-item">
                            <span class="order-item-name"><?php echo $array[$i]['name'] ?></span>
                            <span class="order-item-phone"><strong><?php echo $array[$i]['phone'] ?></strong></span>
                            <span class="order-item-status">
                                <label class="status"> <?php echo $array[$i]['status'] ?> </label>
                            </span>
                            <span><?php echo $array[$i]['time'] ?> </span>
                        </p>
                    </li><?php } ?>
                </ul>
            </div>
        </div>
    </div>
<?php 
}
add_shortcode( 'don_hang', 'donhang' );
add_filter('widget_text', 'do_shortcode');
?>