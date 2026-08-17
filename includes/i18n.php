<?php
/**
 * Lightweight EN/VI translation for the public-facing pages only. Admin and
 * staff management pages intentionally stay Vietnamese-only and never call
 * t(), so they're unaffected regardless of the visitor's language choice.
 */

function current_lang(): string
{
    $requested = $_GET['lang'] ?? null;
    if ($requested === 'en' || $requested === 'vi') {
        $_SESSION['lang'] = $requested;
    }
    $lang = $_SESSION['lang'] ?? 'vi';
    return $lang === 'en' ? 'en' : 'vi';
}

function t(string $key, array $vars = []): string
{
    global $translations;
    $lang = current_lang();
    $text = $translations[$key][$lang] ?? $translations[$key]['vi'] ?? $key;
    foreach ($vars as $k => $v) {
        $text = str_replace('{' . $k . '}', $v, $text);
    }
    return $text;
}

$translations = [
    'site.tagline' => ['vi' => 'Chia sẻ tài nguyên editing', 'en' => 'Editing resource sharing'],
    'nav.all_categories' => ['vi' => 'Tất cả danh mục', 'en' => 'All categories'],
    'nav.search_placeholder' => ['vi' => 'Tìm kiếm tài nguyên...', 'en' => 'Search resources...'],
    'nav.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'nav.post_resource' => ['vi' => '+ Đăng tài nguyên', 'en' => '+ Post resource'],
    'nav.submit_resource' => ['vi' => '+ Gửi tài nguyên', 'en' => '+ Submit resource'],
    'nav.favorites' => ['vi' => 'Mục yêu thích', 'en' => 'Favorites'],
    'nav.feedback' => ['vi' => 'Gửi góp ý', 'en' => 'Send feedback'],
    'nav.feedback_short' => ['vi' => 'Góp ý', 'en' => 'Feedback'],
    'nav.logout' => ['vi' => 'Đăng xuất', 'en' => 'Log out'],
    'nav.login' => ['vi' => 'Đăng nhập', 'en' => 'Log in'],
    'nav.register' => ['vi' => 'Đăng ký', 'en' => 'Sign up'],

    'footer.blurb' => ['vi' => 'Nơi chia sẻ pack chỉnh sửa, project file, mask, plugin After Effects và raw cut miễn phí cho cộng đồng editor.', 'en' => 'A place to share editing packs, project files, masks, After Effects plugins and raw cuts free for the editor community.'],
    'footer.categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
    'footer.account' => ['vi' => 'Tài khoản', 'en' => 'Account'],
    'footer.info' => ['vi' => 'Thông tin', 'en' => 'Info'],
    'footer.all_resources' => ['vi' => 'Tất cả tài nguyên', 'en' => 'All resources'],

    'home.latest_title' => ['vi' => 'Tài nguyên mới nhất', 'en' => 'Latest resources'],
    'home.search_results_for' => ['vi' => 'Kết quả cho "{q}"', 'en' => 'Results for "{q}"'],
    'home.resource_count' => ['vi' => '{n} tài nguyên', 'en' => '{n} resources'],
    'home.all_pill' => ['vi' => 'Tất cả', 'en' => 'All'],
    'home.categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
    'home.clear_filter' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear filter'],
    'home.empty_title' => ['vi' => 'Không tìm thấy tài nguyên phù hợp.', 'en' => 'No matching resources found.'],
    'home.view_detail' => ['vi' => 'Xem chi tiết', 'en' => 'View details'],
    'anonymous' => ['vi' => 'Ẩn danh', 'en' => 'Anonymous'],
    'home.donate_qr_alt' => ['vi' => 'Mã QR chuyển khoản ủng hộ', 'en' => 'Donation bank transfer QR code'],
    'home.donate_close' => ['vi' => 'Đóng', 'en' => 'Close'],
    'home.donate_bank' => ['vi' => 'Ngân hàng', 'en' => 'Bank'],
    'home.donate_holder' => ['vi' => 'Chủ tài khoản', 'en' => 'Account holder'],
    'home.donate_account' => ['vi' => 'Số tài khoản', 'en' => 'Account number'],
    'home.donate_copy' => ['vi' => 'Copy', 'en' => 'Copy'],
    'home.donate_copied' => ['vi' => 'Đã copy!', 'en' => 'Copied!'],

    'detail.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
    'detail.download' => ['vi' => 'Tải xuống / Xem nguồn', 'en' => 'Download / View source'],
    'detail.favorite' => ['vi' => 'Yêu thích', 'en' => 'Favorite'],
    'detail.posted_by' => ['vi' => 'Đăng bởi', 'en' => 'Posted by'],
    'detail.description' => ['vi' => 'Mô tả', 'en' => 'Description'],
    'detail.info' => ['vi' => 'Thông tin', 'en' => 'Info'],
    'detail.category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'detail.status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'detail.status_active' => ['vi' => 'Đang hoạt động', 'en' => 'Active'],
    'detail.status_disable' => ['vi' => 'Đã tắt', 'en' => 'Disabled'],
    'detail.posted_date' => ['vi' => 'Ngày đăng', 'en' => 'Posted on'],
    'detail.updated_date' => ['vi' => 'Cập nhật', 'en' => 'Updated'],
    'detail.related' => ['vi' => 'Có thể bạn cũng thích', 'en' => 'You might also like'],
    'detail.not_found_title' => ['vi' => 'Không tìm thấy', 'en' => 'Not found'],
    'detail.not_found_text' => ['vi' => 'Tài nguyên không tồn tại hoặc đã bị gỡ.', 'en' => 'This resource does not exist or has been removed.'],
    'detail.back_home' => ['vi' => 'Về trang chủ', 'en' => 'Back to home'],

    'comments.title' => ['vi' => 'Bình luận ({n})', 'en' => 'Comments ({n})'],
    'comments.placeholder' => ['vi' => 'Viết bình luận...', 'en' => 'Write a comment...'],
    'comments.submit' => ['vi' => 'Gửi bình luận', 'en' => 'Post comment'],
    'comments.login_prompt' => ['vi' => 'Đăng nhập', 'en' => 'Log in'],
    'comments.login_suffix' => ['vi' => 'để bình luận.', 'en' => 'to comment.'],
    'comments.empty' => ['vi' => 'Chưa có bình luận nào. Hãy là người đầu tiên!', 'en' => 'No comments yet. Be the first!'],
    'comments.err_empty' => ['vi' => 'Vui lòng nhập nội dung bình luận.', 'en' => 'Please enter a comment.'],
    'comments.err_too_long' => ['vi' => 'Bình luận quá dài.', 'en' => 'Comment is too long.'],
    'comments.pending_notice' => ['vi' => 'Đã gửi bình luận, đang chờ quản trị viên duyệt.', 'en' => 'Comment submitted, awaiting moderator approval.'],

    'favorites.title' => ['vi' => 'Mục yêu thích', 'en' => 'Favorites'],
    'favorites.empty' => ['vi' => 'Bạn chưa yêu thích tài nguyên nào.', 'en' => 'You haven\'t favorited any resources yet.'],
    'favorites.explore' => ['vi' => 'Khám phá tài nguyên', 'en' => 'Explore resources'],

    'feedback.title' => ['vi' => 'Gửi góp ý', 'en' => 'Send feedback'],
    'feedback.subtitle' => ['vi' => 'Chia sẻ ý kiến của bạn để {site} tốt hơn', 'en' => 'Share your thoughts to help {site} improve'],
    'feedback.err_empty' => ['vi' => 'Vui lòng nhập nội dung góp ý.', 'en' => 'Please enter your feedback.'],
    'feedback.err_too_long' => ['vi' => 'Nội dung góp ý quá dài.', 'en' => 'Feedback is too long.'],
    'feedback.thanks' => ['vi' => 'Cảm ơn bạn đã gửi góp ý!', 'en' => 'Thanks for your feedback!'],
    'feedback.label' => ['vi' => 'Nội dung góp ý', 'en' => 'Your feedback'],
    'feedback.submit' => ['vi' => 'Gửi góp ý', 'en' => 'Send feedback'],

    'submit.title' => ['vi' => 'Gửi tài nguyên', 'en' => 'Submit a resource'],
    'submit.subtitle' => ['vi' => 'Chia sẻ pack chỉnh sửa, project file hoặc tài nguyên của bạn với cộng đồng. Tài nguyên sẽ hiển thị công khai sau khi được duyệt.', 'en' => 'Share your editing pack, project file, or resource with the community. It will be shown publicly once approved.'],
    'submit.err_title' => ['vi' => 'Vui lòng nhập tiêu đề.', 'en' => 'Please enter a title.'],
    'submit.err_category' => ['vi' => 'Vui lòng chọn danh mục.', 'en' => 'Please choose a category.'],
    'submit.err_source' => ['vi' => 'Vui lòng nhập đường dẫn tải xuống hợp lệ.', 'en' => 'Please enter a valid download link.'],
    'submit.err_cover_type' => ['vi' => 'Ảnh bìa phải là JPG, PNG, WEBP hoặc GIF.', 'en' => 'Cover image must be JPG, PNG, WEBP or GIF.'],
    'submit.err_cover_size' => ['vi' => 'Ảnh bìa tối đa 8MB.', 'en' => 'Cover image must be under 8MB.'],
    'submit.err_cover_upload' => ['vi' => 'Tải ảnh bìa lên thất bại, vui lòng thử lại.', 'en' => 'Cover image upload failed, please try again.'],
    'submit.err_cover_required' => ['vi' => 'Vui lòng chọn ảnh bìa.', 'en' => 'Please choose a cover image.'],
    'submit.success' => ['vi' => 'Gửi tài nguyên thành công! Tài nguyên của bạn đang chờ quản trị viên duyệt.', 'en' => 'Submitted! Your resource is awaiting moderator approval.'],
    'submit.field_title' => ['vi' => 'Tiêu đề', 'en' => 'Title'],
    'submit.field_category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'submit.field_category_placeholder' => ['vi' => '— Chọn danh mục —', 'en' => '— Choose a category —'],
    'submit.field_author' => ['vi' => 'Tên tác giả hiển thị', 'en' => 'Display author name'],
    'submit.field_source' => ['vi' => 'Đường dẫn tải xuống (Google Drive, Mega...)', 'en' => 'Download link (Google Drive, Mega...)'],
    'submit.field_description' => ['vi' => 'Mô tả', 'en' => 'Description'],
    'submit.field_description_placeholder' => ['vi' => 'Nội dung pack bao gồm những gì...', 'en' => 'What does this pack include...'],
    'submit.field_cover' => ['vi' => 'Ảnh bìa', 'en' => 'Cover image'],
    'submit.field_cover_hint' => ['vi' => 'Ảnh sẽ được lưu trữ qua ImgBB. Tối đa 8MB.', 'en' => 'Image is hosted via ImgBB. Max 8MB.'],
    'submit.submit_button' => ['vi' => 'Gửi tài nguyên', 'en' => 'Submit resource'],

    'auth.login_title' => ['vi' => 'Chào mừng trở lại', 'en' => 'Welcome back'],
    'auth.login_subtitle' => ['vi' => 'Đăng nhập để lưu tài nguyên yêu thích và đăng bài', 'en' => 'Log in to save favorites and post resources'],
    'auth.err_bad_login' => ['vi' => 'Tên đăng nhập hoặc mật khẩu không đúng.', 'en' => 'Incorrect username or password.'],
    'auth.google_login' => ['vi' => 'Đăng nhập với Google', 'en' => 'Log in with Google'],
    'auth.google_register' => ['vi' => 'Đăng ký với Google', 'en' => 'Sign up with Google'],
    'auth.or' => ['vi' => 'hoặc', 'en' => 'or'],
    'auth.identifier' => ['vi' => 'Tên đăng nhập hoặc email', 'en' => 'Username or email'],
    'auth.password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'auth.login_button' => ['vi' => 'Đăng nhập', 'en' => 'Log in'],
    'auth.no_account' => ['vi' => 'Chưa có tài khoản?', 'en' => 'Don\'t have an account?'],
    'auth.register_now' => ['vi' => 'Đăng ký ngay', 'en' => 'Sign up now'],

    'auth.register_title' => ['vi' => 'Tạo tài khoản', 'en' => 'Create an account'],
    'auth.register_subtitle' => ['vi' => 'Tham gia cộng đồng chia sẻ tài nguyên editing', 'en' => 'Join the editing resource sharing community'],
    'auth.err_username' => ['vi' => 'Tên đăng nhập phải từ 3-50 ký tự (chữ, số, dấu . hoặc _).', 'en' => 'Username must be 3-50 characters (letters, numbers, . or _).'],
    'auth.err_email' => ['vi' => 'Email không hợp lệ.', 'en' => 'Invalid email address.'],
    'auth.err_full_name' => ['vi' => 'Vui lòng nhập họ tên.', 'en' => 'Please enter your full name.'],
    'auth.err_password_length' => ['vi' => 'Mật khẩu phải có ít nhất 6 ký tự.', 'en' => 'Password must be at least 6 characters.'],
    'auth.err_password_mismatch' => ['vi' => 'Mật khẩu xác nhận không khớp.', 'en' => 'Passwords do not match.'],
    'auth.err_taken' => ['vi' => 'Tên đăng nhập hoặc email đã được sử dụng.', 'en' => 'Username or email is already taken.'],
    'auth.register_success' => ['vi' => 'Đăng ký thành công! Chào mừng bạn đến với {site}.', 'en' => 'Registration successful! Welcome to {site}.'],
    'auth.username' => ['vi' => 'Tên đăng nhập', 'en' => 'Username'],
    'auth.full_name' => ['vi' => 'Họ và tên', 'en' => 'Full name'],
    'auth.email' => ['vi' => 'Email', 'en' => 'Email'],
    'auth.confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm password'],
    'auth.register_button' => ['vi' => 'Đăng ký', 'en' => 'Sign up'],
    'auth.have_account' => ['vi' => 'Đã có tài khoản?', 'en' => 'Already have an account?'],

    'time.just_now' => ['vi' => 'vừa xong', 'en' => 'just now'],
    'time.minutes_ago' => ['vi' => '{n} phút trước', 'en' => '{n} minutes ago'],
    'time.hours_ago' => ['vi' => '{n} giờ trước', 'en' => '{n} hours ago'],
    'time.days_ago' => ['vi' => '{n} ngày trước', 'en' => '{n} days ago'],
];
