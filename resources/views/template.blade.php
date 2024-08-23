<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <style>
    @import url('https://fonts.googleapis.com/css?family=Assistant');

body {
    background: #eee;
    font-family: Assistant, sans-serif
}

.cell-1 {
  border-collapse: separate;
  border-spacing: 0 4em;
  background: #ffffff;
  border-bottom: 5px solid transparent;
  /*background-color: gold;*/
  background-clip: padding-box;
  cursor: pointer;
}

thead {
  background: #dddcdc;
}


.table-elipse {
  cursor: pointer;
}

#demo {
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s 0.1s ease-in-out;
  transition: all 0.3s ease-in-out;
}

.row-child {
  background-color: #000;
  color: #fff;
}</style>
</head>
<body>
<div class="container mt-5">
        <div class="d-flex justify-content-center row">
            <div class="col-md-10">
                <div class="rounded">
                    <div class="table-responsive table-borderless">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">S. No.</th>
                                    <th>Order #</th>
                                    <th>Company name</th>
                                    <th>status</th>
                                    <th>Total</th>
                                    <th>Created</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <tr class="cell-1" data-toggle="collapse" data-target="#demo-1">
                                    <td class="text-center">1</td>
                                    <td>#SO-13487</td>
                                    <td>Gasper Antunes</td>
                                    <td><span class="badge badge-danger">Fullfilled</span></td>
                                    <td>$2674.00</td>
                                    <td>Today</td>
                                    <td class="table-elipse" data-toggle="collapse" data-target="#demo-1"><i class="fa fa-ellipsis-h text-black-50"></i></td>
                                </tr>
                                <tr id="demo-1" class="collapse cell-1 row-child">
                                    <td class="text-center" colspan="1"><i class="fa fa-angle-up"></i></td>
                                    <td colspan="1">Product&nbsp;</td>
                                    <td colspan="3">iphone SX with ratina display</td>
                                    <td colspan="1">QTY</td>
                                    <td colspan="2">2</td>
                                </tr>
                                <tr class="cell-1" data-toggle="collapse" data-target="#demo-2">
                                    <td class="text-center">2</td>
                                    <td>#SO-13488</td>
                                    <td>Tinder Steel</td>
                                    <td><span class="badge badge-success">Fullfilled</span></td>
                                    <td>$3664.00</td>
                                    <td>Yesterday</td>
                                    <td class="table-elipse" data-toggle="collapse" data-target="#demo-2"><i class="fa fa-ellipsis-h text-black-50"></i></td>
                                </tr>
                                <tr id="demo-2" class="collapse cell-1 row-child">
                                    <td class="text-center" colspan="1"><i class="fa fa-angle-up"></i></td>
                                    <td colspan="1">Product&nbsp;</td>
                                    <td colspan="3">iphone SX with ratina display</td>
                                    <td colspan="1">QTY</td>
                                    <td colspan="2">2</td>
                                </tr>
                                <tr class="cell-1" data-toggle="collapse" data-target="#demo-3">
                                    <td class="text-center">3</td>
                                    <td>#SO-13489</td>
                                    <td>Micro Steel</td>
                                    <td><span class="badge badge-success">Placed</span></td>
                                    <td>$2674.00</td>
                                    <td>March 20, 2020</td>
                                    <td class="table-elipse" data-toggle="collapse" data-target="#demo-3"><i class="fa fa-ellipsis-h text-black-50"></i></td>
                                </tr>
                                <tr id="demo-3" class="collapse cell-1 row-child">
                                    <td class="text-center" colspan="1"><i class="fa fa-angle-up"></i></td>
                                    <td colspan="1">Product&nbsp;</td>
                                    <td colspan="3">iphone SX with ratina display</td>
                                    <td colspan="1">QTY</td>
                                    <td colspan="2">2</td>
                                </tr>
                                <tr class="cell-1" data-toggle="collapse" data-target="#demo-4">
                                    <td class="text-center">4</td>
                                    <td>#SO-13490</td>
                                    <td>B Mobiles</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>$4674.00</td>
                                    <td>March 22, 2020</td>
                                    <td class="table-elipse" data-toggle="collapse" data-target="#demo-4"><i class="fa fa-ellipsis-h text-black-50"></i></td>
                                </tr>
                                <tr id="demo-4" class="collapse cell-1 row-child">
                                    <td class="text-center" colspan="1"><i class="fa fa-angle-up"></i></td>
                                    <td colspan="1">Product&nbsp;</td>
                                    <td colspan="3">iphone SX with ratina display</td>
                                    <td colspan="1">QTY</td>
                                    <td colspan="2">2</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
