function setUserInfo(userInfo) {
  wx.setStorageSync('userInfo', userInfo);
}

function getUserInfo() {
  return wx.getStorageSync('userInfo') || null;
}

function setCart(cartList) {
  wx.setStorageSync('cartList', cartList);
  
  const userInfo = getUserInfo();
  if (!userInfo) return; // 没登录绝不上报数据

  const { request } = require('./request.js');
  request({
    url: 'cart_sync.php',
    method: 'POST',
    data: { 
      goodsList: cartList,
      user_id: userInfo.id,
      nickname: userInfo.nickname
    }
  }).catch(() => {});
}

function getCart() {
  return wx.getStorageSync('cartList') || [];
}

module.exports = { setUserInfo, getUserInfo, setCart, getCart };