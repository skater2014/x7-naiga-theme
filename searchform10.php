    <div class="main-nav-icons" onclick="toggleSearch()">
        <i id="search-icon" class="fas fa-magnifying-glass"></i>
    </div>
    
    <form id="search-form" class="search-form_2" role="search" method="get" action="https://gamewidth.net/">
        <input type="search" class="search-field" id="s" name="s" placeholder="Type then hit Enter..." data-placeholder="Type then hit Enter..." value="">
        <i id="close-icon" class="fas fa-times" onclick="toggleSearch()"></i>
    </form>


<style>

    .main-nav-icons {
        position:absolute;
        right: 240px;

    }



/* 虫眼鏡アイコンのスタイル */
.main-nav-icons .fa-magnifying-glass {
    font-size: 20px;
    cursor: pointer;
}




/* Xアイコンのスタイル */
#close-icon {
    font-size: 20px;
    cursor: pointer;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    visibility: hidden;
}

/* アイコンとフォームをラップする親要素のスタイル */
.search-wrapper {
    position: relative;
    display: inline-block;
    text-align: center; /* 中央配置 */
}

.search-form_2 {
    position: absolute;
    /*top: 1%;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 20px); /* 親要素の幅から余白を引いた幅 */
    left: 340px;
    /*top: 75px;
    /*max-width: 1200px; /* 最大幅1200px */
    text-align: center;
    box-sizing: border-box;
    visibility: hidden;
    transition: visibility 0.3s ease;
    /*margin-top: -21px;*/
    margin: 0px auto;
    width:600px;
    display:inline-block;
}


/*.search-form_2 input[type="search"] {
    background: none;
    border: none;
    box-shadow: none;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--link-color-blue);
}*/

.search-wrapper.show .search-form_2 {
    visibility: visible;
    z-index: 10;
}

.search-wrapper.show #close-icon {
    visibility: visible;
}


</style>




    <script>
        function toggleSearch() {
            var searchForm = document.querySelector(".search-form_2");
            var searchContainer = document.querySelector(".search-container");
            var searchIcon = document.getElementById("search-icon");
            var closeIcon = document.getElementById("close-icon");

            if (searchForm.style.visibility === "visible") {
                searchForm.style.visibility = "hidden";
                searchContainer.classList.remove("show");
            } else {
                searchForm.style.visibility = "visible";
                searchContainer.classList.add("show");
            }
        }
    </script>


