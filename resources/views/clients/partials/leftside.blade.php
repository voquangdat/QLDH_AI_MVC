    -----------------------CARTEGPRY----------------------------------------------
    <section class="cartegory">
        <div class="container">
            <div class="cartegory-top row">
                <p><a style="color:#000000;" href="{{ route('home') }}">Trang chủ</a></p>
                <span>&#8594;</span>
                <p>{{ $currentCategory?->category_name ?? 'Danh mục' }}</p>
                @if ($currentSubcategory)
                <span>&#8594;</span>
                <p>{{ $currentSubcategory->subcategory_name }}</p>
                @endif
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="cartegory-left">
                    <ul>
                        @foreach ($categories as $cat)
                        {{-- Li được thêm class active nếu đang là category đang chọn --}}
                        <li class="cartegory-left-li {{ $currentCategory?->category_id == $cat->category_id ? 'active' : '' }}">
                            <a href="{{ route('category.index', ['category_id' => $cat->category_id]) }}">
                                {{ $cat->category_name }}
                            </a>
                            @if ($cat->subcategories->count())
                            <ul>
                                @foreach ($cat->subcategories as $sub)
                                <li class="{{ $currentSubcategory?->subcategory_id == $sub->subcategory_id ? 'active' : '' }}">
                                    <a href="{{ route('category.index', ['subcategory_id' => $sub->subcategory_id]) }}">
                                        {{ $sub->subcategory_name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>  <!-- End cartegory-left -->