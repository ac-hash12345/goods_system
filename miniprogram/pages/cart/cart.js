// cart.js - 购物车页面逻辑
const { getCart, setCart, getUserInfo, setUserInfo } = require('../../utils/storage');
const { request } = require('../../utils/request');

Page({
  data: {
    cartList: [],
    selectAll: true,
    totalPrice: 0
  },

  onShow() { this.refreshCart(); },

  refreshCart() {
    let cartList = getCart();
    
    // 🌟 核心修复1：清洗“幽灵数据”，强制转为严格的布尔值
    cartList = cartList.map(item => {
      if (typeof item.checked === 'undefined') {
        item.checked = true;
      }
      return item;
    });
    setCart(cartList); // 洗净后存回内存

    const selectAll = cartList.length > 0 ? cartList.every((item) => item.checked) : false;
    this.setData({ cartList, selectAll });
    this.calcTotal();
  },

  calcTotal() {
    const totalPrice = this.data.cartList.reduce((sum, item) => {
      // 🌟 核心修复2：严格判断
      if (!item.checked) return sum;
      return sum + Number(item.price) * Number(item.quantity);
    }, 0);
    this.setData({ totalPrice: totalPrice.toFixed(2) });
  },

  toggleItem(e) {
    const index = e.currentTarget.dataset.index;
    const cartList = this.data.cartList.slice();
    cartList[index].checked = !cartList[index].checked; 
    setCart(cartList);
    this.setData({ cartList, selectAll: cartList.every((item) => item.checked) });
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
    if (type === 'add') { current.quantity += 1; } else if (current.quantity > 1) { current.quantity -= 1; }
    setCart(cartList);
    this.setData({ cartList });
    this.calcTotal();
  },

  removeItem(e) {
    const index = e.currentTarget.dataset.index;
    wx.showModal({
      title: '确认删除',
      content: '确定移除该商品吗？',
      success: (res) => {
        if (!res.confirm) return;
        const cartList = this.data.cartList.slice();
        cartList.splice(index, 1);
        setCart(cartList);
        this.setData({ cartList, selectAll: cartList.every((item) => item.checked) });
        this.calcTotal();
      }
    });
  },

  submitOrder() {
    // 🌟 核心修复3：增加未登录拦截和余额同步
    const userInfo = getUserInfo();
    if (!userInfo) {
      wx.showToast({ title: '请先登录', icon: 'none' });
      setTimeout(() => wx.navigateTo({ url: '/pages/login/login' }), 1000);
      return;
    }

    const selectedGoods = this.data.cartList.filter((item) => item.checked);
    if (selectedGoods.length === 0) {
      wx.showToast({ title: '请选择商品', icon: 'none' });
      return;
    }

    wx.showLoading({ title: '支付中...', mask: true });
    request({
      url: 'order_create.php',
      method: 'POST',
      data: { goodsList: selectedGoods, user_id: userInfo.id }
    }).then((res) => {
      wx.hideLoading();
      wx.showToast({ title: '支付成功', icon: 'success' });
      
      // 更新本地余额
      userInfo.balance = res.data.new_balance.toFixed(2);
      setUserInfo(userInfo);

      const remain = this.data.cartList.filter((item) => !item.checked);
      setCart(remain);
      this.setData({ cartList: remain, selectAll: false, totalPrice: 0 });
      this.calcTotal();
    }).catch((err) => {
      wx.hideLoading();
      setTimeout(() => {
        wx.showToast({ title: err.msg || '支付失败', icon: 'error', duration: 2000 });
      }, 100);
    });
  }
});