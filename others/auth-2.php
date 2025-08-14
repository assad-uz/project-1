<?php
if ($user['role_id'] == 1) {
    // Admin panel access
} else {
    // Customer panel access
}

/*
 ব্যবহার (লজিকের মধ্যে):
আপনি লগইন করার পর ইউজারের role_id চেক করবেন।

Yes, Admin-এর জন্য আলাদা টেবিল লাগবে না।
আপনার বর্তমান user + role সেটআপ যথেষ্ট শক্তিশালী ও স্ট্যান্ডার্ড।
*/
?>