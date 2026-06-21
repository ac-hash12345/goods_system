// list.js - 订单列表页面逻辑

const { request } = require('../../../utils/request');

Page({
  data: {
    orderList: []
  },

  onLoad(options) {
    if (options.create === '1') {
      this.createOrderFromDetail(options.id, options.quantity);
    } else {
      this.fetchOrders();
    }
  },

  onShow() {
    this.fetchOrders();
  },

  onPullDownRefresh() {
    this.fetchOrders().finally(() => wx.stopPullDownRefresh());
  },

  fetchOrders() {
    return request({
      url: 'order_list.php'
    }).then((res) => {
      this.setData({ orderList: res.data.list || [] });
    });
  },

  createOrderFromDetail(id, quantity) {
    request({
      url: 'order_create.php',
      method: 'POST',
      data: {
        goodsList: [{ id, quantity: Number(quantity) || 1 }]
      }
    }).then(() => {
      wx.showToast({ title: '下单成功' });
      wx.navigateBack({ delta: 1 });
    });
  }
});