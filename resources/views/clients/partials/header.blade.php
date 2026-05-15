<section class="top">
    <div class="container">
        <div class="row">
            <div class="menu-bar"><i class="fas fa-bars"></i></div>

            <div class="top-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/clients/image/logoShop.png') }}" alt="Voxfootball Logo" height="40" width="48">
                </a>
            </div>

            <div class="top-menu-items">
                <ul>
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Trang chủ</a></li>

                    {{-- Danh mục động từ DB --}}
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('category.index', ['category_id' => $category->category_id]) }}">
                            {{ $category->category_name }}
                            @if($category->subcategories->count())
                            <i class="fas fa-chevron-down"></i>
                            @endif
                        </a>
                        @if($category->subcategories->count())
                        <ul class="top-menu-item">
                            @foreach($category->subcategories as $sub)
                            <li>
                                <a href="{{ route('category.index', ['subcategory_id' => $sub->subcategory_id]) }}">
                                    <i class="fas fa-tag"></i> {{ $sub->subcategory_name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @endforeach

                    <li><a href="{{ route('news') }}"><i class="fas fa-newspaper"></i> Tin tức</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-phone"></i> Liên hệ</a></li>
                </ul>
            </div>

            <div class="top-menu-icons">
                <ul>
                    <li>
                        <input type="text" placeholder="Tìm kiếm..." id="search-input">
                        <i class="fas fa-search"></i>
                    </li>

                    <li class="user-account">
                        @if(Auth::check() && !Auth::user()->isAdmin())
                        {{-- User thường đã đăng nhập --}}
                        <a href="#" class="user-dropdown-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-name">{{ auth()->user()->fullname }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="user-dropdown-menu">
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <i class="fas fa-user"></i> Thông tin cá nhân
                            </a>
                            <a href="{{ route('orders') }}" class="dropdown-item">
                                <i class="fas fa-shopping-bag"></i> Đơn hàng
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                        @else
                        {{-- Chưa đăng nhập hoặc đang dùng tài khoản admin --}}
                        <div class="auth-buttons">
                            <a href="{{ route('login') }}" class="auth-btn login-btn">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                            <span class="auth-separator">|</span>
                            <a href="{{ route('register') }}" class="auth-btn register-btn">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </div>
                        @endif
                    </li>

                    <li>
                        <x-mini-cart />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>