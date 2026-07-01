let cart = [];

function addToCart(id, name, price, stock) {
    const existing = cart.find(item => item.id === id);
    const currentQty = existing ? existing.qty : 0;

    if (currentQty + 1 > stock) {
        alert('Not enough stock for ' + name);
        return;
    }

    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1, stock: stock });
    }

    renderCart();
}

function changeQty(id, delta) {
    const item = cart.find(item => item.id === id);
    if (!item) return;

    const newQty = item.qty + delta;

    if (newQty <= 0) {
        cart = cart.filter(item => item.id !== id);
    } else if (newQty > item.stock) {
        alert('Not enough stock for ' + item.name);
    } else {
        item.qty = newQty;
    }

    renderCart();
}

function renderCart() {
    const cartBody = document.getElementById('cartBody');
    const totalEl = document.getElementById('cartTotal');
    cartBody.innerHTML = '';

    let total = 0;

    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        total += subtotal;

        const row = document.createElement('div');
        row.className = 'cart-item';
        
        const isAdmin = (window.USER_ROLE === 'admin');
        
        if (isAdmin) {
            row.innerHTML =
                '<span>' + item.name + '</span>' +
                '<span class="qty-controls">' +
                'Qty: <input type="number" class="pos-input" value="' + item.qty + '" min="1" style="width: 55px; text-align: center; margin: 0 4px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 4px;" onchange="updateCartQty(' + item.id + ', this.value)">' +
                '</span>' +
                '<span class="price-controls" style="display: inline-flex; align-items: center;">' +
                'TSh <input type="number" class="pos-input" value="' + item.price + '" min="0" step="0.01" style="width: 90px; text-align: right; margin: 0 4px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 4px;" onchange="updateCartPrice(' + item.id + ', this.value)">' +
                '</span>';
        } else {
            row.innerHTML =
                '<span>' + item.name + '</span>' +
                '<span class="qty-controls">' +
                '<button type="button" onclick="changeQty(' + item.id + ', -1)">-</button> ' +
                item.qty +
                ' <button type="button" onclick="changeQty(' + item.id + ', 1)">+</button>' +
                '</span>' +
                '<span>TSh ' + subtotal.toLocaleString() + '</span>';
        }
        
        cartBody.appendChild(row);
    });

    totalEl.textContent = 'TSh ' + total.toLocaleString();
}

function updateCartQty(id, val) {
    const item = cart.find(item => item.id === id);
    if (!item) return;

    let newQty = parseInt(val, 10);
    if (isNaN(newQty) || newQty <= 0) {
        newQty = 1;
    }
    
    item.qty = newQty;
    renderCart();
}

function updateCartPrice(id, val) {
    const item = cart.find(item => item.id === id);
    if (!item) return;

    let newPrice = parseFloat(val);
    if (isNaN(newPrice) || newPrice < 0) {
        newPrice = 0;
    }

    item.price = newPrice;
    renderCart();
}

function submitOrder() {
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }

    const baseUrl = window.BASE_URL || '';
    const paymentMethod = document.getElementById('paymentMethod').value;

    fetch(baseUrl + '/cashier/process_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            payment_method: paymentMethod,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.qty,
                unit_price: item.price
            }))
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = baseUrl + '/cashier/billing.php?id=' + data.order_id;
        } else {
            alert(data.message || 'Order failed');
        }
    })
    .catch(() => alert('Could not connect to the server'));
}
