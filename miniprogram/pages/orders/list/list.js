const { request } = require('../../../utils/request');
const { getUserInfo, setUserInfo } = require('../../../utils/storage');

Page({
  data: { orderList: [], isCreating: false },

  onLoad(options) {
    if (options.create === '1') {
      this.createOrderFromDetail(options.id, options.quantity);
    } else {
      this.fetchOrders();
    }
  },

  onShow() {
    if (!this.data.isCreating) { this.fetchOrders(); }
  },

  onPullDownRefresh() {
    this.fetchOrders().finally(() => wx.stopPullDownRefresh());
  },

  fetchOrders() {
    const userInfo = getUserInfo();
    if (!userInfo) {
      this.setData({ orderList: [] });
      return Promise.resolve();
    }
    return request({
      url: 'order_list.php',
      data: { user_id: userInfo.id }
    }).then((res) => {
      this.setData({ orderList: res.data.list || [] });
    });
  },

  createOrderFromDetail(id, quantity) {
    const userInfo = getUserInfo();
    if (!userInfo) {
      wx.showToast({ title: '请先登录', icon: 'none' });
      setTimeout(() => wx.navigateBack({ delta: 1 }), 1000);
      return;
    }

    this.setData({ isCreating: true });
    wx.showLoading({ title: '支付中...', mask: true });

    request({
      url: 'order_create.php',
      method: 'POST',
      data: { goodsList: [{ id, quantity: Number(quantity) || 1 }], user_id: userInfo.id }
    }).then((res) => {
      wx.hideLoading();
      wx.showToast({ title: '支付成功' });
      
      userInfo.balance = res.data.new_balance.toFixed(2);
      setUserInfo(userInfo);

      this.setData({ isCreating: false });
      this.fetchOrders(); 
    }).catch((err) => {
      wx.hideLoading();
      wx.showToast({ title: err.msg || '支付失败', icon: 'none', duration: 2000 });
      this.setData({ isCreating: false });
      setTimeout(() => wx.navigateBack({ delta: 1 }), 2000);
    });
  }
});