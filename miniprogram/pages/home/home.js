// home.js - 首页逻辑

const { request } = require('../../utils/request');

Page({
  data: {
    // banners: [
    //   'https://picsum.photos/seed/goods-1/750/360',
    //   'https://picsum.photos/seed/goods-2/750/360',
    //   'https://picsum.photos/seed/goods-3/750/360'
    // ],
    banners: [
      '/images/banner1.jpg', 
      '/images/banner2.jpg',
      '/images/banner3.jpg'
    ],
    categories: [
      { id: 1, name: '数码' },
      { id: 2, name: '服饰' },
      { id: 3, name: '食品' },
      { id: 4, name: '家居' }
    ],
    goodsList: [],
    keyword: '',
    loading: false
  },

  onLoad() {
    this.fetchGoods();
  },

  onPullDownRefresh() {
    this.fetchGoods().finally(() => wx.stopPullDownRefresh());
  },

  fetchGoods() {
    this.setData({ loading: true });
    return request({
      url: 'goods_list.php',
      data: {
        keyword: this.data.keyword,
        page: 1,
        pageSize: 8
      }
    }).then((res) => {
      this.setData({ goodsList: res.data.list || [] });
    }).finally(() => {
      this.setData({ loading: false });
    });
  },

  onInput(e) {
    this.setData({ keyword: e.detail.value });
  },

  onSearch() {
    this.fetchGoods();
  },

  toDetail(e) {
    const { id } = e.currentTarget.dataset;
    wx.navigateTo({ url: `/pages/goods/detail/detail?id=${id}` });
  }
});