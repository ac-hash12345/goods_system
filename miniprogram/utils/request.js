const config = require('./config');

function request(options) {
  return new Promise((resolve, reject) => {
    wx.request({
      url: `${config.apiBase}/${options.url}`,
      method: options.method || 'GET',
      data: options.data || {},
      header: {
        'content-type': 'application/json'
      },
      success(res) {
        if (res.data && res.data.code === 0) {
          resolve(res.data);
          return;
        }
        wx.showToast({ title: (res.data && res.data.msg) || '请求失败', icon: 'none' });
        reject(res.data);
      },
      fail(err) {
        wx.showToast({ title: '网络错误', icon: 'none' });
        reject(err);
      }
    });
  });
}

module.exports = {
  request
};