<?php
session_start();

// Cart ထဲမှာ ပစ္စည်းတွေ ရှိနေရင် အကုန်လုံး ဖျက်ဖို့ 
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']); // Session ထဲက cart ကို ဖျက်ဖို့ သုံးထားတာ 
}

header("Location: cart.php?success=" . urlencode("All items removed from cart!"));
exit();
?>