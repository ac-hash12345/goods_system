// cart.js - 购物车页面逻辑

const { getCart, setCart } = require('../../utils/storage');
const { request } = require('../../utils/request');

Page({
  data: {
    cartList: [],
    selectAll: true,
    totalPrice: 0
  },

  onShow() {
    this.refreshCart();
  },

  refreshCart() {
    const cartList = getCart();
    const selectAll = cartList.length > 0 ? cartList.every((item) => item.checked !== false) : false;
    this.setData({ cartList, selectAll });
    this.calcTotal();
  },

  calcTotal() {
    const totalPrice = this.data.cartList.reduce((sum, item) => {
      if (item.checked === false) {
        return sum;
      }
      return sum + Number(item.price) * Number(item.quantity);
    }, 0);
    this.setData({ totalPrice: totalPrice.toFixed(2) });
  },

  toggleItem(e) {
    const index = e.currentTarget.dataset.index;
    const cartList = this.data.cartList.slice();
    cartList[index].checked = !cartList[index].checked;
    setCart(cartList);
    this.setData({ cartList, selectAll: cartList.every((item) => item.checked !== false) });
    this.calcTotal();
  },

  toggleAll() {
    const selectAll = !this.data.selectAll;
    const cartList = this.data.cartList.map((item) => ({ ...item, checked: selectAll }));
    setCart(cartList);
    this.setData({ cartList, selectAll });
    this.calcTotal();
  },

  changeQuantity(e) {
    const { index, type } = e.currentTarget.dataset;
    const cartList = this.data.cartList.slice();
    const current = cartList[index];
    if (type === 'add') {
      current.quantity += 1;
    } else if (current.quantity > 1) {
      current.quantity -= 1;
    }
    setCart(cartList);
    this.setData({ cartList });
    this.calcTotal();
  },

  removeItem(e) {
    const index = e.currentTarget.dataset.index;
    wx.showModal({
      title: '确认删除',
      content: '确定要从购物车移除该商品吗？',
      success: (res) => {
        if (!res.confirm) {
          return;
        }
        const cartList = this.data.cartList.slice();
        cartList.splice(index, 1);
        setCart(cartList);
        this.setData({ cartList, selectAll: cartList.every((item) => item.checked !== false) });
        this.calcTotal();
      }
    });
  },

  submitOrder() {
    const selectedGoods = this.data.cartList.filter((item) => item.checked !== false);
    if (selectedGoods.length === 0) {
      wx.showToast({ title: '请选择商品', icon: 'none' });
      return;
    }

    request({
      url: 'order_create.php',
      method: 'POST',
      data: { goodsList: selectedGoods }
    }).then(() => {
      wx.showToast({ title: '下单成功' });
      const remain = this.data.cartList.filter((item) => item.checked === false);
      setCart(remain);
      this.setData({ cartList: remain, selectAll: false, totalPrice: 0 });
      this.calcTotal();
    });
  }
});