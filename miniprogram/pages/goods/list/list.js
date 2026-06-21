// list.js - 商品列表页面逻辑

const { request } = require('../../../utils/request');

Page({
  data: {
    goodsList: [],
    page: 1,
    pageSize: 8,
    total: 0,
    keyword: '',
    loading: false
  },

  onLoad(options) {
    if (options.keyword) {
      this.setData({ keyword: options.keyword });
    }
    this.fetchGoods(true);
  },

  onPullDownRefresh() {
    this.fetchGoods(true).finally(() => wx.stopPullDownRefresh());
  },

  onReachBottom() {
    if (this.data.goodsList.length < this.data.total) {
      this.setData({ page: this.data.page + 1 });
      this.fetchGoods(false);
    }
  },

  onInput(e) {
    this.setData({ keyword: e.detail.value });
  },

  onSearch() {
    this.fetchGoods(true);
  },

  fetchGoods(reset) {
    if (this.data.loading) {
      return Promise.resolve();
    }

    this.setData({ loading: true });
    const page = reset ? 1 : this.data.page;
    return request({
      url: 'goods_list.php',
      data: {
        keyword: this.data.keyword,
        page,
        pageSize: this.data.pageSize
      }
    }).then((res) => {
      const list = reset ? res.data.list : this.data.goodsList.concat(res.data.list || []);
      this.setData({
        goodsList: list,
        total: res.data.total,
        page
      });
    }).finally(() => {
      this.setData({ loading: false });
    });
  },

  toDetail(e) {
    wx.navigateTo({ url: `/pages/goods/detail/detail?id=${e.currentTarget.dataset.id}` });
  }
});