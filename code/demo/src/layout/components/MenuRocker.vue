<template>
    <div class="nav-container primary-menu">
        <div class="mobile-topbar-header">
            <div>
                <img src="../../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
            </div>
            <div>
                <h4 class="logo-text text-primary fw-bold">SmartTrial</h4>
            </div>
            <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
            </div>
        </div>
        <nav class="navbar navbar-expand-xl w-100">
            <ul class="navbar-nav justify-content-start flex-grow-1 gap-1">
                <li class="nav-item">
                    <router-link to="/dang-ky">
                        <a class="nav-link" href="/dang-ky">
                            <div class="parent-icon"><i class="fa-solid fa-user-plus"></i>
                            </div>
                            <div class="menu-title">Đăng Ký</div>
                        </a>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/my-schedule">
                        <a class="nav-link" href="/my-schedule">
                            <div class="parent-icon"><i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div class="menu-title">Xếp Lịch Học Thử</div>
                        </a>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/my-classes">
                        <a class="nav-link" href="/my-classes">
                            <div class="parent-icon"><i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <div class="menu-title">Quản Lý Lớp Học</div>
                        </a>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/face-id">
                        <a class="nav-link" href="/face-id">
                            <div class="parent-icon"><i class="fa-solid fa-id-badge"></i>
                            </div>
                            <div class="menu-title">Xác Thực Face ID</div>
                        </a>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/thanh-toan">
                        <a class="nav-link" href="/thanh-toan">
                            <div class="parent-icon"><i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div class="menu-title">Thanh Toán Học Phí</div>
                        </a>
                    </router-link>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
export default {

}
</script>
<style></style>