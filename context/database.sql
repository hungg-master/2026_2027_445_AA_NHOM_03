-- =====================================================================
-- Study Schedule Platform — Initial Schema
-- Matches the reviewed design (Nhóm_3_final.drawio)
-- Run this in Supabase: SQL Editor -> New query -> paste -> Run
-- =====================================================================

create extension if not exists pgcrypto with schema extensions;

-- =====================================================================
-- 1. admins
-- =====================================================================
create table admins (
  id             uuid primary key default gen_random_uuid(),
  email          text not null unique,
  mat_khau       text not null,
  ho_ten         text,
  so_dien_thoai  text
);

-- =====================================================================
-- 2. phong_hocs (rooms)
-- =====================================================================
create table phong_hocs (
  id        uuid primary key default gen_random_uuid(),
  so_phong  text,
  dia_chi   text,
  mo_ta     text
);

-- =====================================================================
-- 3. mon_hocs (subjects)
-- =====================================================================
create table mon_hocs (
  id            uuid primary key default gen_random_uuid(),
  ten_mon_hoc   text not null,
  mo_ta         text,
  lop           text,
  tinh_trang    text
);

-- =====================================================================
-- 4. hoc_viens (students)
-- =====================================================================
create table hoc_viens (
  id             uuid primary key default gen_random_uuid(),
  ho_ten         text,
  email          text not null unique,
  mat_khau       text not null,
  so_dien_thoai  text,
  ngay_sinh      date,
  gioi_tinh      text,
  dia_chi        text
);

-- =====================================================================
-- 5. giao_viens (teachers)
-- =====================================================================
create table giao_viens (
  id                    uuid primary key default gen_random_uuid(),
  ho_ten                text,
  email                 text not null unique,
  mat_khau              text not null,
  so_dien_thoai         text,
  ngay_sinh             date,
  gioi_tinh             text,
  so_nam_kinh_nghiem    integer,
  chuc_danh             text,                                -- e.g. Thạc sĩ, Cử nhân, Giáo viên giỏi
  giao_vien_da_duyet    uuid references admins(id),          -- nullable: admin who approved this teacher
  trang_thai_duyet      text check (trang_thai_duyet in ('cho_duyet','da_duyet','tu_choi'))
                        default 'cho_duyet'
);

-- =====================================================================
-- 6. lop_hocs (classes)
-- =====================================================================
create table lop_hocs (
  id                    uuid primary key default gen_random_uuid(),
  id_giao_vien          uuid references giao_viens(id) not null,
  id_mon_hoc            uuid references mon_hocs(id) not null,
  id_phong_hoc          uuid references phong_hocs(id),      -- nullable when online
  loai_lop              text check (loai_lop in ('dai_tra','kem')),
  hinh_thuc             text check (hinh_thuc in ('online','offline')),
  link_online           text,                                 -- used only when hinh_thuc = 'online'
  hoc_phi               numeric(12,2) default 0,
  si_so_toi_da          integer,
  thoi_gian_bat_dau     timestamptz,
  thoi_gian_ket_thuc    timestamptz,
  tinh_trang            text
);

-- =====================================================================
-- 7. dang_ky_lops (class registrations)
-- =====================================================================
create table dang_ky_lops (
  id             uuid primary key default gen_random_uuid(),
  id_lop_hoc     uuid references lop_hocs(id) not null,
  id_hoc_vien    uuid references hoc_viens(id) not null,
  ngay_dang_ky   timestamptz default now(),
  trang_thai     text,
  constraint uq_dang_ky_lop_hoc_vien unique (id_lop_hoc, id_hoc_vien)
);

-- =====================================================================
-- 8. xac_thuc_face_ids (face ID verification)
-- =====================================================================
create table xac_thuc_face_ids (
  id                    uuid primary key default gen_random_uuid(),
  id_dang_ky            uuid references dang_ky_lops(id) unique,  -- one verification per registration
  id_hoc_vien           uuid references hoc_viens(id),
  thoi_gian_xac_thuc    timestamptz,
  trang_thai            text
);

-- =====================================================================
-- 9. thoi_gian_ranhs (free time — teacher OR student, exactly one set)
-- =====================================================================
create table thoi_gian_ranhs (
  id                    uuid primary key default gen_random_uuid(),
  id_giao_vien          uuid references giao_viens(id),
  id_hoc_vien           uuid references hoc_viens(id),
  loai_nguoi_dung       text check (loai_nguoi_dung in ('giao_vien','hoc_vien')) not null,
  ngay_trong_tuan       smallint check (ngay_trong_tuan between 0 and 6),
  thoi_gian_bat_dau     time,
  thoi_gian_ket_thuc    time,
  trang_thai            text,
  constraint chk_thoi_gian_ranh_owner check (
    (loai_nguoi_dung = 'giao_vien' and id_giao_vien is not null and id_hoc_vien is null)
    or
    (loai_nguoi_dung = 'hoc_vien' and id_hoc_vien is not null and id_giao_vien is null)
  )
);

-- =====================================================================
-- 10. thong_baos (notifications)
-- =====================================================================
create table thong_baos (
  id                uuid primary key default gen_random_uuid(),
  id_hoc_vien       uuid references hoc_viens(id),
  loai_thong_bao    text check (loai_thong_bao in ('hoc_phi','buoi_hoc')),
  noi_dung          text,
  da_gui            boolean default false,
  thoi_gian_gui     timestamptz
);

-- =====================================================================
-- 11. thanh_toans (payments)
-- =====================================================================
create table thanh_toans (
  id                      uuid primary key default gen_random_uuid(),
  id_dang_ky              uuid references dang_ky_lops(id),
  so_tien                 numeric(12,2),
  trang_thai              text check (trang_thai in ('cho_xu_ly','thanh_cong','that_bai','hoan_tien')),
  phuong_thuc             text,
  ma_giao_dich            text,
  thoi_gian_thanh_toan    timestamptz
);

-- =====================================================================
-- 12. cuoc_tro_chuyens (chat threads)
-- =====================================================================
create table cuoc_tro_chuyens (
  id                        uuid primary key default gen_random_uuid(),
  id_hoc_vien               uuid references hoc_viens(id) not null,
  id_giao_vien              uuid references giao_viens(id),        -- nullable: null for AI-only threads
  loai_cuoc_tro_chuyen      text check (loai_cuoc_tro_chuyen in ('AI','users')),
  tin_nhan_cuoi_luc         timestamptz,
  ngay_tao                  timestamptz default now()
);

-- =====================================================================
-- 13. tin_nhans (chat messages)
-- =====================================================================
create table tin_nhans (
  id                    uuid primary key default gen_random_uuid(),
  id_cuoc_tro_chuyen    uuid references cuoc_tro_chuyens(id) not null,
  id_nguoi_gui          uuid,        -- points into giao_viens.id or hoc_viens.id depending on loai_nguoi_gui;
                                      -- resolve by matching against the parent thread's id_giao_vien/id_hoc_vien
  loai_nguoi_gui        text check (loai_nguoi_gui in ('users','AI')),
  noi_dung              text,
  da_doc                boolean default false,
  duoc_tao_vao          timestamptz default now()
);

-- =====================================================================
-- Recommended indexes (safe to run — no effect on data, speeds up lookups)
-- =====================================================================
create index idx_lop_hocs_giao_vien        on lop_hocs(id_giao_vien);
create index idx_lop_hocs_mon_hoc          on lop_hocs(id_mon_hoc);
create index idx_dang_ky_lops_lop_hoc      on dang_ky_lops(id_lop_hoc);
create index idx_dang_ky_lops_hoc_vien     on dang_ky_lops(id_hoc_vien);
create index idx_thoi_gian_ranhs_giao_vien on thoi_gian_ranhs(id_giao_vien);
create index idx_thoi_gian_ranhs_hoc_vien  on thoi_gian_ranhs(id_hoc_vien);
create index idx_thanh_toans_dang_ky       on thanh_toans(id_dang_ky);
create index idx_tin_nhans_thread          on tin_nhans(id_cuoc_tro_chuyen);
create index idx_cuoc_tro_chuyens_hoc_vien on cuoc_tro_chuyens(id_hoc_vien);
create index idx_cuoc_tro_chuyens_giao_vien on cuoc_tro_chuyens(id_giao_vien);
