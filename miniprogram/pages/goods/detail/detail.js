const { request } = require('../../../utils/request');
const { getCart, setCart, getUserInfo } = require('../../../utils/storage');

Page({
  data: {
    goods: null,
    quantity: 1
  },

  onLoad(options) {
    this.goodsId = options.id;
    this.fetchDetail();
  },

  fetchDetail() {
    request({ url: 'goods_detail.php', data: { id: this.goodsId } }).then((res) => {
      this.setData({ goods: res.data.goods });
    });
  },

  addToCart() {
    if (!getUserInfo()) {
      wx.showToast({ title: '请先登录', icon: 'none' });
      setTimeout(() => wx.navigateTo({ url: '/pages/login/login' }), 1000);
      return;
    }

    const goods = this.data.goods;
    if (!goods) return;

    const cart = getCart();
    const index = cart.findIndex((item) => item.id === goods.id);
    if (index >= 0) {
      cart[index].quantity += this.data.quantity;
    } else {
      cart.push({ id: goods.id, name: goods.name, price: goods.price, cover: goods.cover, stock: goods.stock, quantity: this.data.quantity, checked: true });
    }
    setCart(cart);
    wx.showToast({ title: '已加入购物车' });
  },

  createOrder() {
    if (!getUserInfo()) {
      wx.showToast({ title: '请先登录', icon: 'none' });
      setTimeout(() => wx.navigateTo({ url: '/pages/login/login' }), 1000);
      return;
    }

    const goods = this.data.goods;
    if (!goods) return;
    wx.navigateTo({ url: `/pages/orders/list/list?create=1&id=${goods.id}&quantity=${this.data.quantity}` });
  }
});