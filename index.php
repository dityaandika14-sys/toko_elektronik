<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Lux | Premium Electronics & Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .premium-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #003d79; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-800 overflow-x-hidden">

    <div id="app">
        <!-- Konten akan dimuat di sini oleh JavaScript -->
    </div>

    <script>
        // --- DATA STATE (SIMULASI DATABASE) ---
        let products = [
            { id: 1, name: "Smart TV 4K Crystal UHD 50\"", price: 6499000, img: "https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?q=80&w=2070&auto=format&fit=crop", brand: "Samsung", category: "Televisi", stock: 15 },
            { id: 2, name: "Kulkas Side by Side 600L", price: 12999000, img: "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=2070&auto=format&fit=crop", brand: "LG", category: "Kulkas", stock: 8 },
            { id: 3, name: "iPhone 15 Pro Max 256GB", price: 22999000, img: "https://images.unsplash.com/photo-1696446701796-da61225697cc?q=80&w=2070&auto=format&fit=crop", brand: "Apple", category: "Smartphone", stock: 20 },
            { id: 4, name: "Mesin Cuci Front Load 9kg", price: 5800000, img: "https://images.unsplash.com/photo-1626806819282-2c1dc61a0e05?q=80&w=2066&auto=format&fit=crop", brand: "Panasonic", category: "Mesin Cuci", stock: 5 }
        ];

        let cart = [];
        let currentView = 'store'; // 'store' atau 'admin'
        let editMode = false;
        let editingId = null;
        let selectedImageBase64 = null;

        // --- NAVIGATION & UTILS ---
        function setView(view) {
            currentView = view;
            renderApp();
            window.scrollTo(0,0);
        }

        function toggleCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('overlay');
            if(sidebar) {
                sidebar.classList.toggle('translate-x-full');
                overlay.classList.toggle('hidden');
                renderCartItems();
            }
        }

        function toggleModal(mode = 'add', id = null) {
            const modal = document.getElementById('admin-modal');
            editMode = (mode === 'edit');
            editingId = id;
            selectedImageBase64 = null;

            modal.classList.toggle('hidden');
            
            if (editMode && id) {
                const p = products.find(prod => prod.id === id);
                if (p) {
                    document.getElementById('modal-title').innerText = "Edit Produk";
                    document.getElementById('in-name').value = p.name;
                    document.getElementById('in-brand').value = p.brand;
                    document.getElementById('in-category').value = p.category;
                    document.getElementById('in-price').value = p.price;
                    document.getElementById('in-stock').value = p.stock;
                    document.getElementById('submit-btn').innerText = "Update Produk";
                }
            } else {
                if (modal.classList.contains('hidden')) return;
                document.getElementById('modal-title').innerText = "Input Inventaris";
                document.getElementById('admin-form').reset();
                document.getElementById('submit-btn').innerText = "Simpan ke Katalog";
            }
        }

        function handleImageUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    selectedImageBase64 = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').innerText = msg;
            toast.classList.remove('translate-y-20');
            setTimeout(() => toast.classList.add('translate-y-20'), 3000);
        }

        // --- LOGIC OPERASIONAL ---
        function addToCart(id) {
            const product = products.find(p => p.id === id);
            cart.push(product);
            const count = document.getElementById('cart-count');
            if(count) count.innerText = cart.length;
            
            const btn = event.currentTarget;
            const original = btn.innerHTML;
            btn.innerHTML = 'Berhasil!';
            btn.classList.add('bg-green-500', 'text-white');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-green-500', 'text-white');
            }, 1000);
        }

        function handleSubmit(e) {
            e.preventDefault();
            const name = document.getElementById('in-name').value;
            const price = parseInt(document.getElementById('in-price').value);
            const stock = parseInt(document.getElementById('in-stock').value);
            const category = document.getElementById('in-category').value;
            const brand = document.getElementById('in-brand').value;

            if (editMode) {
                const idx = products.findIndex(p => p.id === editingId);
                if (idx !== -1) {
                    products[idx] = {
                        ...products[idx],
                        name, price, stock, category, brand,
                        img: selectedImageBase64 || products[idx].img
                    };
                    showToast("Produk berhasil diperbarui!");
                }
            } else {
                const newProd = {
                    id: Date.now(),
                    name, price, stock, category, brand,
                    img: selectedImageBase64 || "https://images.unsplash.com/photo-1550009158-9ebf69173e03?q=80&w=2101&auto=format&fit=crop" 
                };
                products.unshift(newProd);
                showToast("Produk berhasil ditambahkan!");
            }

            toggleModal();
            renderApp();
        }

        function deleteProduct(id) {
            if(confirm("Hapus produk ini secara permanen dari sistem?")) {
                products = products.filter(p => p.id !== id);
                renderApp();
                showToast("Produk telah dihapus.");
            }
        }

        // --- COMPONENT RENDERERS ---
        function renderCartItems() {
            const container = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            if(!container) return;

            if (cart.length === 0) {
                container.innerHTML = '<p class="text-center text-slate-400 py-10">Keranjang masih kosong.</p>';
            } else {
                container.innerHTML = cart.map((item) => `
                    <div class="flex gap-4 p-2 border-b border-slate-50 items-center">
                        <img src="${item.img}" class="w-12 h-12 object-cover rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-xs font-semibold line-clamp-1">${item.name}</h4>
                            <p class="text-xs font-bold text-[#003d79]">Rp ${item.price.toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `).join('');
            }
            const total = cart.reduce((sum, item) => sum + item.price, 0);
            totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function renderApp() {
            const app = document.getElementById('app');
            
            if (currentView === 'store') {
                app.innerHTML = `
                    <!-- Top Bar -->
                    <div class="bg-[#003d79] text-white py-2 text-[10px] px-4 md:px-12 flex justify-between items-center font-medium uppercase tracking-wider">
                        <div><i class="fa-solid fa-location-dot mr-2 text-yellow-400"></i> Temukan Toko Terdekat</div>
                        <div class="hidden md:flex gap-6">
                            <button onclick="setView('admin')" class="hover:text-yellow-400"><i class="fa-solid fa-lock mr-1"></i> Admin Panel</button>
                            <span>Bantuan</span>
                            <span>Konfirmasi Pesanan</span>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="sticky top-0 z-50 glass-morphism border-b border-slate-200">
                        <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between gap-4">
                            <div class="text-2xl font-bold text-[#003d79] flex items-center gap-2 cursor-pointer" onclick="setView('store')">
                                <div class="bg-yellow-400 p-1 rounded-md"><i class="fa-solid fa-bolt text-[#003d79]"></i></div>
                                <span>E-LUX</span>
                            </div>
                            <div class="flex-1 max-w-2xl relative hidden md:block">
                                <input type="text" placeholder="Cari mesin cuci, kulkas, atau smartphone..." class="w-full pl-10 pr-4 py-2.5 rounded-full border border-slate-200 focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400"></i>
                            </div>
                            <div class="flex items-center gap-6 text-[#003d79]">
                                <button class="relative hover:text-yellow-500 transition-colors" onclick="toggleCart()">
                                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                                    <span id="cart-count" class="absolute -top-2 -right-2 bg-yellow-400 text-[#003d79] text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">${cart.length}</span>
                                </button>
                                <button class="flex items-center gap-2 font-bold text-sm bg-slate-100 px-4 py-2 rounded-full">
                                    <i class="fa-solid fa-user-circle text-xl"></i>
                                    <span class="hidden sm:inline">Masuk</span>
                                </button>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero Section -->
                    <section class="max-w-7xl mx-auto px-4 md:px-8 py-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2 relative group rounded-3xl overflow-hidden h-[300px] md:h-[450px]">
                                <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent flex flex-col justify-center px-12 text-white">
                                    <span class="bg-yellow-400 text-[#003d79] px-3 py-1 rounded-full text-[10px] font-black w-fit mb-4 uppercase">Promo Eksklusif</span>
                                    <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Teknologi Mewah<br>Kini Lebih Terjangkau</h1>
                                    <p class="text-slate-200 mb-8 max-w-md">Nikmati cicilan 0% dan garansi resmi untuk setiap pembelian produk flagship pilihan.</p>
                                    <button class="bg-yellow-400 text-[#003d79] px-8 py-3 rounded-full font-bold hover:bg-yellow-500 transition-all w-fit shadow-lg shadow-yellow-200/20">Cek Sekarang</button>
                                </div>
                            </div>
                            <div class="flex flex-col gap-4">
                                <div class="bg-[#eef5ff] p-6 rounded-3xl flex-1 border border-blue-100 flex flex-col justify-between">
                                    <h3 class="text-lg font-bold text-[#003d79]">Smartphone Terbaru</h3>
                                    <p class="text-xs text-slate-500">Hemat hingga 2 Juta Rupiah.</p>
                                    <img src="https://images.unsplash.com/photo-1616348436168-de43ad0db179?q=80&w=1981&auto=format&fit=crop" class="h-24 object-contain self-end">
                                </div>
                                <div class="bg-yellow-50 p-6 rounded-3xl flex-1 border border-yellow-100">
                                    <h3 class="text-lg font-bold text-slate-800">Layanan Purna Jual</h3>
                                    <p class="text-xs text-slate-500 mt-1">Teknisi ahli siap membantu di 50+ lokasi.</p>
                                    <div class="mt-4 flex gap-2">
                                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-[#003d79] shadow-sm"><i class="fa-solid fa-headset text-xs"></i></div>
                                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-[#003d79] shadow-sm"><i class="fa-solid fa-wrench text-xs"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Product Grid -->
                    <section class="max-w-7xl mx-auto px-4 md:px-8 py-12">
                        <div class="flex justify-between items-end mb-10">
                            <div>
                                <h2 class="text-2xl font-bold text-[#003d79]">Katalog Pilihan</h2>
                                <p class="text-slate-400 text-sm mt-1">Menampilkan stok terbaru yang tersedia hari ini.</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="w-10 h-10 border border-slate-200 rounded-full flex items-center justify-center hover:bg-slate-50"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                                <button class="w-10 h-10 border border-slate-200 rounded-full flex items-center justify-center hover:bg-slate-50"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            ${products.map(p => `
                                <div class="product-card bg-white rounded-2xl p-4 premium-shadow flex flex-col group border border-slate-100">
                                    <div class="relative overflow-hidden rounded-xl h-48 mb-4">
                                        <img src="${p.img}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div class="absolute top-2 left-2 bg-white/90 backdrop-blur px-2.5 py-1 rounded-lg text-[10px] font-bold text-[#003d79] uppercase tracking-widest">${p.brand}</div>
                                    </div>
                                    <h3 class="font-bold text-slate-800 text-sm line-clamp-2 mb-2 flex-1">${p.name}</h3>
                                    <div class="mb-4">
                                        <div class="text-[10px] text-slate-400 line-through">Rp ${(p.price * 1.15).toLocaleString('id-ID')}</div>
                                        <span class="text-lg font-bold text-[#003d79]">Rp ${p.price.toLocaleString('id-ID')}</span>
                                        <div class="mt-1 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Tersedia ${p.stock} Unit</span>
                                        </div>
                                    </div>
                                    <button onclick="addToCart(${p.id})" class="w-full bg-[#eef5ff] text-[#003d79] py-3 rounded-xl font-bold hover:bg-yellow-400 hover:text-[#003d79] transition-all text-xs flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-cart-plus"></i> Tambahkan
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </section>

                    <!-- Cart Sidebar -->
                    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl z-[60] transform translate-x-full transition-transform duration-300">
                        <div class="p-6 h-full flex flex-col">
                            <div class="flex justify-between items-center mb-8">
                                <h2 class="font-bold text-lg text-[#003d79]">Keranjang Saya</h2>
                                <button onclick="toggleCart()" class="text-slate-300 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                            </div>
                            <div id="cart-items" class="flex-1 overflow-y-auto space-y-4"></div>
                            <div class="border-t pt-6 mt-4">
                                <div class="flex justify-between mb-6">
                                    <span class="text-slate-400 text-sm">Estimasi Total</span>
                                    <span id="cart-total" class="font-bold text-xl text-[#003d79]"></span>
                                </div>
                                <button class="w-full bg-[#003d79] text-white py-4 rounded-2xl font-bold hover:bg-blue-900 transition-all shadow-lg shadow-blue-200">Proses Pembayaran</button>
                            </div>
                        </div>
                    </div>
                    <div id="overlay" onclick="toggleCart()" class="fixed inset-0 bg-black/40 z-[55] hidden"></div>
                `;
            } else {
                // VIEW ADMIN
                app.innerHTML = `
                    <div class="flex min-h-screen">
                        <!-- Sidebar Admin -->
                        <aside class="w-64 bg-[#003d79] text-white hidden md:flex flex-col sticky top-0 h-screen">
                            <div class="p-8 text-2xl font-bold flex items-center gap-2 border-b border-blue-900/50">
                                <div class="bg-yellow-400 p-1 rounded-md text-[#003d79]"><i class="fa-solid fa-bolt"></i></div>
                                <span>E-LUX <span class="text-[10px] text-yellow-400 block font-light tracking-widest -mt-1">ADMIN PANEL</span></span>
                            </div>
                            <nav class="flex-1 p-6 space-y-3 mt-4">
                                <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 font-bold">
                                    <i class="fa-solid fa-box w-5"></i> Manajemen Stok
                                </a>
                                <a href="#" onclick="setView('store')" class="flex items-center gap-3 p-3 rounded-xl text-slate-400 hover:text-white transition-all">
                                    <i class="fa-solid fa-eye w-5"></i> Lihat Website
                                </a>
                            </nav>
                            <div class="p-8 border-t border-blue-900/50">
                                <button onclick="setView('store')" class="text-red-400 hover:text-red-300 font-bold flex items-center gap-2 text-sm transition-colors">
                                    <i class="fa-solid fa-right-from-bracket"></i> Keluar Admin
                                </button>
                            </div>
                        </aside>

                        <!-- Main Admin -->
                        <main class="flex-1">
                            <header class="bg-white border-b border-slate-100 px-8 py-5 flex justify-between items-center sticky top-0 z-40">
                                <div>
                                    <h1 class="text-xl font-bold text-slate-800">Inventaris Produk</h1>
                                    <p class="text-[11px] text-slate-400 uppercase tracking-tighter font-bold">Update terakhir: Baru saja</p>
                                </div>
                                <button onclick="toggleModal('add')" class="bg-[#003d79] text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 hover:bg-blue-900 shadow-xl shadow-blue-100 transition-all active:scale-95">
                                    <i class="fa-solid fa-plus"></i> Tambah Produk Baru
                                </button>
                            </header>

                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                                        <p class="text-slate-400 text-xs font-bold uppercase mb-1">Total Katalog</p>
                                        <h3 class="text-3xl font-bold text-[#003d79]">${products.length} <span class="text-sm font-normal text-slate-400">Items</span></h3>
                                    </div>
                                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                                        <p class="text-slate-400 text-xs font-bold uppercase mb-1">Kategori Aktif</p>
                                        <h3 class="text-3xl font-bold text-[#003d79]">6 <span class="text-sm font-normal text-slate-400">Sektor</span></h3>
                                    </div>
                                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                                        <p class="text-slate-400 text-xs font-bold uppercase mb-1">Status Sistem</p>
                                        <h3 class="text-2xl font-bold text-green-500">Operasional</h3>
                                    </div>
                                </div>

                                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                    <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">Daftar Produk Aktif</span>
                                        <input type="text" placeholder="Cari di tabel..." class="text-xs px-4 py-2 rounded-lg border border-slate-200 outline-none w-48">
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                                <tr>
                                                    <th class="px-8 py-4">Informasi Produk</th>
                                                    <th class="px-6 py-4">Kategori</th>
                                                    <th class="px-6 py-4">Harga Unit</th>
                                                    <th class="px-6 py-4">Stok</th>
                                                    <th class="px-6 py-4 text-center">Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 text-sm">
                                                ${products.map(p => `
                                                    <tr class="hover:bg-slate-50/80 transition-colors group">
                                                        <td class="px-8 py-5">
                                                            <div class="flex items-center gap-4">
                                                                <img src="${p.img}" class="w-10 h-10 object-cover rounded-xl shadow-sm">
                                                                <div>
                                                                    <div class="font-bold text-slate-800">${p.name}</div>
                                                                    <div class="text-[10px] text-slate-400 uppercase font-medium">${p.brand}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-5">
                                                            <span class="bg-blue-50 text-[#003d79] px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase">${p.category}</span>
                                                        </td>
                                                        <td class="px-6 py-5 font-mono font-bold text-[#003d79]">Rp ${p.price.toLocaleString('id-ID')}</td>
                                                        <td class="px-6 py-5 font-bold text-slate-600">${p.stock}</td>
                                                        <td class="px-6 py-5 text-center">
                                                            <div class="flex justify-center gap-3">
                                                                <button onclick="toggleModal('edit', ${p.id})" class="text-blue-400 hover:text-blue-600 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                                                <button onclick="deleteProduct(${p.id})" class="text-red-300 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>

                    <!-- Modal Admin -->
                    <div id="admin-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal()"></div>
                        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden">
                            <div class="bg-[#003d79] p-8 text-white flex justify-between items-center">
                                <div>
                                    <h3 id="modal-title" class="text-xl font-bold">Input Inventaris</h3>
                                    <p class="text-[10px] text-blue-200 uppercase mt-1">Lengkapi spesifikasi perangkat</p>
                                </div>
                                <button onclick="toggleModal()" class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-all"><i class="fa-solid fa-xmark text-xl"></i></button>
                            </div>
                            <form id="admin-form" onsubmit="handleSubmit(event)" class="p-10 space-y-5 text-sm">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Nama Produk</label>
                                    <input type="text" id="in-name" required placeholder="Contoh: iPad Pro M2" class="w-full px-5 py-3.5 border border-slate-100 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Brand</label>
                                        <input type="text" id="in-brand" required placeholder="Apple" class="w-full px-5 py-3.5 border border-slate-100 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Kategori</label>
                                        <select id="in-category" class="w-full px-5 py-3.5 border border-slate-100 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none appearance-none cursor-pointer">
                                            <option>Smartphone</option>
                                            <option>Televisi</option>
                                            <option>Kulkas</option>
                                            <option>Audio</option>
                                            <option>Mesin Cuci</option>
                                            <option>AC</option>
                                            <option>Kipas Angin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Harga (IDR)</label>
                                        <input type="number" id="in-price" required placeholder="0" class="w-full px-5 py-3.5 border border-slate-100 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Stok Unit</label>
                                        <input type="number" id="in-stock" required placeholder="0" class="w-full px-5 py-3.5 border border-slate-100 rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Gambar Produk (Opsional)</label>
                                    <input type="file" accept="image/*" onchange="handleImageUpload(event)" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#003d79] hover:file:bg-blue-100 cursor-pointer">
                                </div>
                                <button type="submit" id="submit-btn" class="w-full bg-[#003d79] text-white py-4 mt-4 rounded-2xl font-bold hover:bg-blue-900 shadow-xl shadow-blue-100 transition-all active:scale-[0.98]">Simpan ke Katalog</button>
                            </form>
                        </div>
                    </div>
                `;
            }
        }

        // --- GLOBAL COMPONENTS ---
        document.body.insertAdjacentHTML('beforeend', `
            <div id="toast" class="fixed bottom-8 right-8 bg-slate-800 text-white px-8 py-4 rounded-[2rem] shadow-2xl translate-y-32 transition-transform duration-500 z-[100] flex items-center gap-4">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white"><i class="fa-solid fa-check text-sm"></i></div>
                <span id="toast-msg" class="text-sm font-bold tracking-tight">Pesan berhasil dikirim</span>
            </div>
        `);

        // INITIAL LOAD
        window.onload = renderApp;
    </script>
</body>
</html>