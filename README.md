Authorize your embedded app using a session token with token exchange.
Luồng này bắt đầu từ khi Merchant (người bán hàng) nhấn nút "Install app" trên Shopify Admin.

Các bước thực hiện:
Yêu cầu cài đặt: Khi Merchant cài app, Shopify gửi một yêu cầu GET đến URL cài đặt của bạn kèm theo các tham số như shop, hmac, và timestamp.

1. Chuyển hướng OAuth: Server (Laravel) kiểm tra chữ ký hmac để đảm bảo yêu cầu đến từ Shopify. Sau đó, nó chuyển hướng Merchant đến trang xác nhận quyền của Shopify.

2. Xác nhận quyền: Merchant xem danh sách các quyền (Scopes) mà app yêu cầu và nhấn "Install".

3. Callback & Trao đổi Token: Shopify gửi Merchant quay lại trang Callback của bạn kèm theo một code. Laravel sẽ dùng code này gửi một yêu cầu bí mật đến Shopify để đổi lấy Access Token vĩnh viễn.

Lưu trữ: Laravel lưu Access Token và tên Shop vào database để dùng cho các API sau này.

4. Khởi tạo App Bridge: Frontend (React) sử dụng Access Token này (thường thông qua JWT/Session Tokens) để giao tiếp với Backend một cách an toàn bên trong Iframe của Shopify.

![Văn bản thay thế](https://cdn.shopify.com/shopifycloud/shopify-dev/p…tion/assets/assets/images/apps/oauth-BVV3KNFj.png)

Authenticate your embedded app using session tokens.

![Văn bản thay thế](https://cdn.shopify.com/shopifycloud/shopify-dev/p…ts/images/apps/auth/jwt-request-flow-DDQ5Q8bW.png)
