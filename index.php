<?php
$products = array(
    array("name" => "Sledgehammer", "price" => 125.75),
    array("name" => "Axe", "price" => 190.50),
    array("name" => "Bandsaw", "price" => 562.13),
    array("name" => "Chisel", "price" => 12.9),
    array("name" => "Hacksaw", "price" => 18.45)
);

require_once 'shoppingCart.php';

$shoppingCart = new ShoppingCart();

$cartContents = '
<div class="alert alert-warning">
    <span class="card-title">The Cart is Empty</span>
</div>';

if (isset($_POST['empty'])) {
    $shoppingCart->clear();
}

if (isset($_POST['add'])) {
    foreach ($products as $product) {
        if ($_POST['name'] == $product['name']) {
            break;
        }
    }

    $shoppingCart->add(array_search($product['name'], array_column($products, 'name')), 1,
        $product['price']
    );
}

if (isset($_POST['remove'])) {
    foreach ($products as $product) {
        if ($_POST['name'] == $product['name']) {
            break;
        }
    }

    $shoppingCart->remove(array_search($product['name'], array_column($products, 'name')));
}

if (!$shoppingCart->isEmpty()) {
    $allItems = $shoppingCart->getItems();

    $cartContents = '
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th class="col-md-7">Product</th>
        <th class="col-md-3 text-center">Quantity</th>
        <th class="col-md-2 text-right">Price</th>
    </tr>
    </thead>
    <tbody>';

    foreach ($allItems as $id => $items) {
        foreach ($items as $item) {
            foreach ($products as $product) {
                if ($id == array_search($product['name'], array_column($products, 'name'))) {
                    break;
                }
            }

            $cartContents .= '
        <tr>
            <td>' . $product['name'] . '</td>
            <td class="text-right">' . $item['quantity'] . '</td>
            <td class="text-right">$' . ($item['price'] * $item['quantity']) . '</td>
            <td class="text-center"><div class="pull-right"><button class="btn orange" onclick=removeItem("' . $product['name'] . '")>Remove</button></div></div></td>
        </tr>';
        }
    }

    $cartContents .= '
    </tbody>
</table>

<div class="text-right">
    <h3>Total:<br />$' . number_format($shoppingCart->getAttributeTotal('price'), 2, '.', ',') . '</h3>
</div>
    <div>
    <button class="btn red" onclick="emptyCart()">Empty Cart</button>
</div>
';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>PHP Shopping Cart</title>

        <style>
            body{margin-top:50px;margin-bottom:200px}
        </style>
    </head>

<body>
    <nav>
        <div class="nav-wrapper">
            <span class="brand-logo">Shopping Cart</span>
        </div>
    </nav>
    <?php
        foreach ($products as $product) {
            echo '
                <div class="row">
                    <div class="col s12 m2">
                        <div class="card">
                            <div class="card-content">
                                
                                <span class="card-title">' . $product['name'] . '</span>
            
                                <div>
                                    <div>
                                        <hp>$' . $product['price'] . '</hp>
                                        <form>
                                            <input type="hidden" value="' . $product['name'] . '" class="product-name" />';

                            echo '
                                            <div>
                                                <button class="waves-effect waves-light btn" onclick=addItem("'. $product['name'] .'")>Add</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>';
            }
    ?>
        <div class="row">
            <div class="col s12 m6">
                <div class="card">
                    <div class="card-content">
                        <?php echo $cartContents; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        const url = 'http://localhost:8000/new?';
        function addItem(productName) {

            let formData = new FormData();
            formData.append('add', '');
            formData.append('name', productName);

            fetch(url,
                {
                    body: formData,
                    method: "post"
                }).then(res => {
                    if(res.status === 200) {
                        location.reload();
                    }
            }).catch(error => console.error('Error:', error));
        }
        function removeItem(productName) {

            let formData = new FormData();
            formData.append('remove', '');
            formData.append('name', productName);

            fetch(url,
                {
                    body: formData,
                    method: "post"
                }).then(res => {
                if(res.status === 200) {
                    location.reload();
                }
            }).catch(error => console.error('Error:', error));
        }
        function emptyCart() {

            let formData = new FormData();
            formData.append('empty', '');

            fetch(url,
                {
                    body: formData,
                    method: "post"
                }).then(res => {
                if(res.status === 200) {
                    location.reload();
                }
            }).catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>