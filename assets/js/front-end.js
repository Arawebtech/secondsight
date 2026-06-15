let cart = {};

// Slugify function for SEO-friendly URLs
function slugify(string) {
  if (!string) return '';
  let slug = string.toString().toLowerCase().trim();
  slug = slug.replace(/[^a-z0-9]+/gi, '-'); // replace non-alphanumeric
  slug = slug.replace(/-+/g, '-'); // collapse multiple hyphens
  return slug.replace(/^-+|-+$/g, ''); // trim hyphens from ends
}

// Elements
const cartContainer = document.getElementById("cartItemsContainer");
const itemCount = document.getElementById("itemCount");
const cartSubtotal = document.getElementById("cartSubtotal");

// ✅ Animations
const style = document.createElement("style");
style.textContent = `
  @keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
  @keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
  }
`;
document.head.appendChild(style);

// ✅ Fetch Cart Data
function fetchCartFromServer() {
  return fetch(`${BASE_URL}ajax/fetch-cart-sidebar.php`)
    .then(res => res.json())
    .then(data => {
      cart = {};
      data.forEach(item => {
        cart[item.id] = {
          id: item.id,
          cart_id: item.cart_id,
          name: item.name,
          price: parseFloat(item.price),
          gst: parseFloat(item.gst || 0),
          actual_price: parseFloat(item.actual_price || item.price),
          qty: parseInt(item.qty),
          image: item.image,
          stock: parseInt(item.stock) || 0
        };
      });

      const totalQty = Object.values(cart).reduce((sum, item) => sum + item.qty, 0);
      renderCart();
      updateCartBadge(totalQty);
      syncHomepageButtons();
      updateCartPageTotals();
    })
    .catch(err => console.error("Fetch Cart Error:", err));
}

// ✅ Render Homepage Buttons
function syncHomepageButtons() {
  document.querySelectorAll(".cart-btn-wrapper").forEach(wrapper => {
    const p_id = wrapper.dataset.id;
    if (!p_id) return;

    const product = cart[p_id];
    if (product) {
      wrapper.innerHTML = `
        <div class="d-flex align-items-center gap-2 cart-actions" data-id="${p_id}">
          <button class="btn btn-sm btn-outline-secondary qty-sub-home" data-id="${p_id}">–</button>
          <span class="fw-bold qty-value" id="qty-${p_id}">${product.qty}</span>
          <button class="btn btn-sm btn-outline-secondary qty-add-home" data-id="${p_id}">+</button>
        </div>
      `;
    } else {
      wrapper.innerHTML = `
        <button class="btn btn-dark flex-fill add-to-cart-btn" 
          data-id="${p_id}" 
          data-name="${wrapper.dataset.name}" 
          data-price="${wrapper.dataset.price}"
          data-photo="${wrapper.dataset.photo}" 
          data-color="${wrapper.dataset.color || ''}" 
          data-size="${wrapper.dataset.size || ''}" 
          data-weight="${wrapper.dataset.weight || ''}" 
          data-unit="${wrapper.dataset.unit || ''}" 
          data-sku="${wrapper.dataset.sku || ''}"
          data-stock="${wrapper.dataset.stock || 0}"
          style="background-color:#161394; border-radius:25px; padding:8px 40px;">
          Add to Cart
        </button>
      `;
    }
  });
}

// ✅ Update Homepage Qty with Stock Check
function updateHomepageQty(p_id, action) {
  const product = cart[p_id];
  if (!product) return;

  // Frontend validation
  if (action === "add" && product.qty >= product.stock) {
    showWarningToast(`Only ${product.stock} in stock`);
    return;
  }

  fetch(`${BASE_URL}ajax/update-quantity.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `cart_id=${product.cart_id}&action=${action}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        showWarningToast(data.error);
      } else if (data.deleted) {
        delete cart[p_id];
        fetchCartFromServer().then(() => {
          updateCartPageTotals();
          if (Object.keys(cart).length === 0) {
            handleEmptyCart();
          }
        });
      } else {
        // Success - refresh cart data
        fetchCartFromServer().then(() => {
          const cartRow = document.querySelector(
            `.cart-item [data-cartid="${product.cart_id}"]`
          )?.closest(".cart-item");

          if (cartRow && cart[p_id]) {
            const qtyEl = cartRow.querySelector("span[id^='qty-']");
            if (qtyEl) qtyEl.textContent = cart[p_id].qty;

            const price = parseFloat(cartRow.dataset.price || 0);
            const qty = cart[p_id].qty;

            const lineTotalEl = cartRow.querySelector("[id^='line-total-']");
            if (lineTotalEl) {
              lineTotalEl.textContent = "₹" + (price * qty).toFixed(2);
            }
          }

          if (!cart[p_id] && cartRow) {
            cartRow.remove();
          }

          updateCartPageTotals();

          if (Object.keys(cart).length === 0) {
            handleEmptyCart();
          }
        });
      }
    })
    .catch(err => {
      console.error("Homepage Qty Error:", err);
      showWarningToast("Failed to update quantity");
    });
}


// ✅ Add to Cart Handler
function addToCartHandler(btn) {
  const wrapper = btn.closest(".cart-btn-wrapper");
  const maxStock = parseInt(wrapper.dataset.stock, 10) || 0;

  if (maxStock <= 0) {
    showWarningToast("Out of stock");
    return;
  }

  btn.disabled = true;

  const formData = new FormData();
  formData.append("p_id", btn.dataset.id);
  formData.append("p_name", btn.dataset.name);
  formData.append("p_price", btn.dataset.price);
  formData.append("p_image", btn.dataset.photo);
  formData.append("p_qty", 1);
  formData.append("p_total_item", 1);
  formData.append("p_color", btn.dataset.color || "");
  formData.append("p_size", btn.dataset.size || "");
  formData.append("p_weight", btn.dataset.weight || "");
  formData.append("p_unit", btn.dataset.unit || "");
  formData.append("p_full_sku", btn.dataset.sku || "");

  fetch(`${BASE_URL}ajax/add-to-cart.php`, { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        showWarningToast(data.error);
      } else {
        showAddToCartSuccess();
        fetchCartFromServer();
      }
    })
    .catch(err => {
      console.error("Add to Cart Error:", err);
      showWarningToast("Failed to add item to cart");
    })
    .finally(() => (btn.disabled = false));
}

// ✅ Render Sidebar Cart
function renderCart() {
  if (!cartContainer) return;
  cartContainer.innerHTML = "";
  let subtotal = 0, totalQty = 0;

  for (let id in cart) {
    const p = cart[id];
    const itemBasePrice = p.price;
    subtotal += p.qty * itemBasePrice;
    totalQty += p.qty;
    
    const item = document.createElement("div");
    item.className = "d-flex border p-2 rounded mb-2";
    item.innerHTML = `
    <a href="${BASE_URL}product/${slugify(p.name)}" style="text-decoration:none; color:black;">
      <img src="${BASE_URL}assets/img/product-detail/${p.image}" 
           class="img-thumbnail me-2" style="width: 80px; height: 80px;">
           </a>
      <div class="flex-grow-1">
      <a href="${BASE_URL}product/${slugify(p.name)}" style="text-decoration:none;color:black;">
        <div class="fw-semibold">${p.name}</div>
        </a>
        <div class="d-flex align-items-center gap-2 my-1">
          <button class="btn btn-sm btn-outline-secondary qty-sub-sidecart" data-cartid="${p.cart_id}">–</button>
          <span class="text-muted small">${p.qty}</span>
          <button class="btn btn-sm btn-outline-secondary qty-add-sidecart" data-cartid="${p.cart_id}">+</button>
        </div>
        <div class="fw-bold text-dark">₹${(p.price * p.qty).toFixed(2)}</div>
        <a href="#" class="text-danger small remove-link" data-cartid="${p.cart_id}">Remove</a>
      </div>
    `;
    cartContainer.appendChild(item);
  }

  if (itemCount) itemCount.textContent = totalQty;
  if (cartSubtotal) cartSubtotal.querySelector("span:last-child").textContent = `₹${subtotal.toFixed(2)}`;
}


// ✅ Centralized Empty Cart Handler
function handleEmptyCart() {
  // Empty-cart UI on cart.php
  const cartPage = document.getElementById("cart-page-container");
  if (cartPage) {
    cartPage.innerHTML = `
      <div class="empty-cart text-center py-5">
        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
        <h3 class="mt-3">Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="${BASE_URL}index.php" class="btn btn-cart secondary mt-3">
          <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
        </a>
      </div>
    `;
  }

  // Reset coupon
  const couponMsg = document.getElementById("coupon-message");
  if (couponMsg) couponMsg.textContent = "";
  const removeBtn = document.querySelector(".btn.btn-link.text-danger");
  if (removeBtn) removeBtn.remove();
  fetch(`${BASE_URL}ajax/remove-coupon.php`, { method: "POST" });

  // Reset totals
  if (document.getElementById("subtotal"))
    document.getElementById("subtotal").textContent = "₹0.00";
  if (document.getElementById("gst"))
    document.getElementById("gst").textContent = "₹0.00";
  if (document.getElementById("discount"))
    document.getElementById("discount").textContent = "-₹0.00";
  if (document.getElementById("grand-total"))
    document.getElementById("grand-total").textContent = "₹0.00";
  if (document.getElementById("coupon-amount"))
    document.getElementById("coupon-amount").value = "0.00";

  // Replace checkout button
  const checkoutBtn = document.querySelector(".btn.btn-cart.mt-3");
  if (checkoutBtn) {
    checkoutBtn.outerHTML = `
      <a href="${BASE_URL}index.php" class="btn btn-cart secondary mt-3">
        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
      </a>`;
  }
}

// ✅ Update Quantity
function updateQuantity(cartId, action) {
  // Find the product in the cart object (if it exists)
  const product = Object.values(cart).find(p => p.cart_id == cartId);
  
  // Find the DOM element for the cart item
  const cartItem = document.querySelector(`.cart-item [data-cartid="${cartId}"]`)?.closest(".cart-item");
  if (!cartItem) return;

  const qtyEl = cartItem.querySelector("span[id^='qty-']");
  const stockMsgEl = cartItem.querySelector("[id^='stock-msg-']");
  if (!qtyEl) return;

  const currentQty = parseInt(qtyEl.textContent || 0, 10);
  const maxStock = parseInt(product?.stock || cartItem.dataset.stock || 0, 10);

  if (action === "add" && maxStock && currentQty >= maxStock) {
    if (stockMsgEl) stockMsgEl.textContent = "Limit reached";
    return;
  }
  if (action === "sub" && currentQty <= 1) {
    if (!confirm("Remove this item from cart?")) return;
  }

  fetch(`${BASE_URL}ajax/update-quantity.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `cart_id=${cartId}&action=${action}`
  })
    .then(res => res.json())
    .then(res => {
      if (res.error) {
        showWarningToast(res.error);
      } else if (res.deleted) {
        cartItem.remove();
        fetchCartFromServer().then(() => {
          updateCartPageTotals();
          if (Object.keys(cart).length === 0) handleEmptyCart();
        });
      } else if (res.success) {
        const newQty = action === "add" ? currentQty + 1 : currentQty - 1;
        qtyEl.textContent = newQty;
        
        const price = parseFloat(cartItem.dataset.price || 0);
        const gst = parseFloat(cartItem.dataset.gst || 0);
        const inclusivePrice = price + gst;
        
        const lineTotalEl = cartItem.querySelector("[id^='line-total-']");
        if (lineTotalEl) lineTotalEl.textContent = "₹" + (price * newQty).toFixed(2);
        
        if (stockMsgEl) stockMsgEl.textContent = "";

        // Trigger global cart update
        fetchCartFromServer().then(() => {
          if (document.getElementById("cart-page-container")) {
            updateCartPageTotals();
          }
        });
      }
    })
    .catch(err => {
      console.error("Qty update error:", err);
      showWarningToast("Failed to update quantity");
    });
}


// ✅ Remove from Cart
function pro_remove(cart_id) {
  const cartItem = document.querySelector(`.remove-link-page[data-cartid="${cart_id}"]`)?.closest(".cart-item");
  if (cartItem) {
    cartItem.style.transition = "opacity 0.3s ease";
    cartItem.style.opacity = "0";
    setTimeout(() => cartItem.remove(), 300);
  }

  fetch(`${BASE_URL}ajax/product-remove.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=cart&cart_id=${cart_id}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        fetchCartFromServer().then(() => {
          updateCartPageTotals();
          if (Object.keys(cart).length === 0) handleEmptyCart();
        });
      } else {
        alert(data.message || "Failed to remove item.");
      }
    })
    .catch(err => console.error("Remove Error:", err));
}

// ✅ Navbar Badge (desktop + mobile)
function updateCartBadge(qty = 0) {
  const desktopBadge = document.getElementById("cart-badge-count");
  const mobileBadge  = document.getElementById("cart-badge-count-mobile");

  if (desktopBadge) desktopBadge.textContent = qty;
  if (mobileBadge)  mobileBadge.textContent  = qty;
}


// ✅ Cart Page Totals
function updateCartPageTotals() {
  let subtotal = 0, gstTotal = 0, totalQty = 0;
  let couponAmount = parseFloat(document.getElementById("coupon-amount")?.value || 0);

  const cartItems = document.querySelectorAll(".cart-item");

  if (cartItems.length === 0) {
    handleEmptyCart();
    return;
  }

  cartItems.forEach(item => {
    const qtyEl = item.querySelector("span[id^='qty-']");
    const qty = parseInt(qtyEl?.textContent || 0, 10);
    const price = parseFloat(item.dataset.price || 0); // base price
    const gst = parseFloat(item.dataset.gst || 0);     // gst amount per unit
    const inclusivePrice = price + gst;

    subtotal += qty * price; 
    gstTotal += qty * gst;
    totalQty += qty;

    const lineTotalEl = item.querySelector("[id^='line-total-']");
    if (lineTotalEl) {
      lineTotalEl.textContent = "₹" + (price * qty).toFixed(2);
    }
  });



  if (couponAmount > subtotal + gstTotal) {
    couponAmount = subtotal + gstTotal;
  }

  if (document.getElementById("subtotal")) {
    document.getElementById("subtotal").textContent = "₹" + subtotal.toFixed(2);
  }
  if (document.getElementById("gst")) {
    document.getElementById("gst").textContent = "₹" + gstTotal.toFixed(2);
  }
  if (document.getElementById("discount")) {
    document.getElementById("discount").textContent = "-₹" + couponAmount.toFixed(2);
  }
  if (document.getElementById("grand-total")) {
    document.getElementById("grand-total").textContent =
      "₹" + (subtotal + gstTotal - couponAmount).toFixed(2);
  }

  // Sync the badge count from here too
  updateCartBadge(totalQty);

  if (document.getElementById("coupon-amount"))
    document.getElementById("coupon-amount").value = couponAmount.toFixed(2);
}



// ✅ Success Toast
function showAddToCartSuccess() {
  const msg = document.createElement("div");
  msg.style.cssText = `
    position: fixed; top: 20px; right: 20px;
    background: #28a745; color: white;
    padding: 15px 20px; border-radius: 8px;
    z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    animation: slideInRight 0.3s ease;
  `;
  msg.innerHTML = '<i class="fas fa-check me-2"></i>Item added to cart!';
  document.body.appendChild(msg);

  setTimeout(() => {
    msg.style.animation = "slideOutRight 0.3s ease";
    setTimeout(() => document.body.removeChild(msg), 300);
  }, 3000);
}

// ✅ Warning Toast
function showWarningToast(msg) {
  const warn = document.createElement("div");
  warn.style.cssText = `
    position: fixed; top: 20px; right: 20px;
    background: #dc3545; color: white;
    padding: 15px 20px; border-radius: 8px;
    z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    animation: slideInRight 0.3s ease;
  `;
  warn.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${msg}`;
  document.body.appendChild(warn);

  setTimeout(() => {
    warn.style.animation = "slideOutRight 0.3s ease";
    setTimeout(() => document.body.removeChild(warn), 300);
  }, 2500);
}

// ✅ Cart Popup
function openCartPopup() {
  const cartPopup = document.getElementById("cartPopup");
  if (!cartPopup) return;
  cartPopup.style.transform = "translateX(0)";
}
function closeCartPopup() {
  const cartPopup = document.getElementById("cartPopup");
  if (!cartPopup) return;
  cartPopup.style.transform = "translateX(100%)";
}

// ✅ Delegated Events
document.addEventListener("click", e => {
  // Homepage qty
  if (e.target.classList.contains("qty-add-home")) {
    updateHomepageQty(e.target.dataset.id, "add");
  }
  if (e.target.classList.contains("qty-sub-home")) {
    updateHomepageQty(e.target.dataset.id, "sub");
  }

// ✅ Sidecart qty
  if (e.target.classList.contains("qty-add-sidecart")) {
    const cartId = e.target.dataset.cartid;
    const p_id = Object.keys(cart).find(id => cart[id].cart_id === cartId);
    if (p_id) updateHomepageQty(p_id, "add");
  }
  if (e.target.classList.contains("qty-sub-sidecart")) {
    const cartId = e.target.dataset.cartid;
    const p_id = Object.keys(cart).find(id => cart[id].cart_id === cartId);
    if (p_id) updateHomepageQty(p_id, "sub");
  }

  // ✅ Cart page qty
  const addBtn = e.target.closest(".qty-add-cart");
  const subBtn = e.target.closest(".qty-sub-cart");

  if (addBtn) {
    updateQuantity(addBtn.dataset.cartid, "add");
  } else if (subBtn) {
    updateQuantity(subBtn.dataset.cartid, "sub");
  }

  // Remove
  if (e.target.classList.contains("remove-link-page") || e.target.classList.contains("remove-link")) {
    e.preventDefault();
    pro_remove(e.target.dataset.cartid);
  }

  // Add to cart
  if (e.target.classList.contains("add-to-cart-btn")) {
    e.preventDefault();
    addToCartHandler(e.target);
  }
});



// ✅ Init
window.addEventListener("DOMContentLoaded", () => {
  fetchCartFromServer();

  const cartIcon = document.getElementById("cart-icon");
  const closeBtn = document.getElementById("close-cart-btn");

  if (cartIcon) {
    cartIcon.addEventListener("click", e => {
      e.preventDefault();
      openCartPopup();
    });
  }
  if (closeBtn) {
    closeBtn.addEventListener("click", e => {
      e.preventDefault();
      closeCartPopup();
    });
  }
});
