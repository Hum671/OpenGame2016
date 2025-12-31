/**
 * 客户端js
 */

"use strict"

const client = {

  /**
   * 域名 - 定义
   * 参数 - 定义
   */
  params: '',
  domain: '',
  host: '',
  respData: null,

  /**
   * 启动程序
   * @return {[type]} [description]
   */
  run: function() {
    this.getToken()
  },

  /**
   * 获取token
   */
  getToken: function() {
    let client = document.getElementById('ysClient')
    this.params = client.getAttribute('params')
    this.domain = client.getAttribute('domain')
    this.host = client.getAttribute('host')
    this.ajaxRequest()
  },

  ajaxRequest: function() {
    var http, _this = this

    if (window.XMLHttpRequest) {
      http = new XMLHttpRequest()
    } else {
      http = new ActiveXObject('Microsoft.XMLHTTP')
    }

    http.open('POST', this.domain+'/api/user/info', true)
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded")
    http.send("token="+this.params)
    _this.style()
    http.onreadystatechange = function() {
      if (http.readyState === 4 && http.status === 200) {
        _this.respData = JSON.parse(http.responseText).data
        _this.buildFloatBox()
      }
    }
  },

  /**
   * 判断Ajax是否返回数据
   * @return {Boolean} [description]
   */
  isResp: function() {
    if (this.respData !== null) {
      return true
    }
    return false
  },

  /**
   * 判断是否为电脑
   * @return {Boolean} [description]
   */
  isPc: function() {
    var u = navigator.userAgent
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1
    var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)

    if (isAndroid || isIOS) return false
    return true
  },

  /**
   * 创建客服浮动框
   */
  buildFloatBox: function() {
    var bigBox = [{
      attr: 'id',
      value: 'ysCustomerBox'
    }, {
      attr: 'class',
      value: this.isResp() && this.respData.is_left === 1
        ? 'ys-float-left' : 'ys-float-right'
    }]
    document.body.appendChild(this.buildDiv(bigBox, 'div'))

    var kfHidden = this.isResp() && this.respData.show_customer === 0
      ? 'ys-hidden' : ''
    var wxHidden = this.isResp() && this.respData.wxchat_url !== '0'
          && this.respData.wxchat_url !== ''
      ? '' : 'ys-hidden'
    var registerHidden = this.isResp() && this.respData.register_link !== '0'
      ? '' : 'ys-hidden'
    var phoneHidden = this.isResp() && this.respData.phone !== '0'
      ? '' : 'ys-hidden'
    var topHidden = this.isResp() && this.respData.show_top === 0
      ? 'ys-hidden' : ''

    var idBigBox = document.getElementById('ysCustomerBox')
    var kf = [{
      attr: 'class',
      value: 'ys-float-ele ys-kf ' + kfHidden
    }, {
      attr: 'onclick',
      value: 'client.handleKf()'
    }], wx = [{
      attr: 'class',
      value: 'ys-float-ele ys-wx ' + wxHidden
    }], phone = [{
      attr: 'class',
      value: 'ys-float-ele ys-phone ' + phoneHidden
    }], register = [{
      attr: 'class',
      value: 'ys-float-ele ys-register ' + registerHidden
    }, {
      attr: 'onclick',
      value: 'client.handleRegister()'
    }], top = [{
      attr: 'class',
      value: 'ys-float-ele ys-top ' + topHidden
    }, {
      attr: 'onclick',
      value: 'client.handleScrollTop()'
    }]

    idBigBox.appendChild(this.buildDiv(kf, 'div'))
    idBigBox.appendChild(this.buildDiv(wx, 'div'))
    idBigBox.appendChild(this.buildDiv(phone, 'div'))
    idBigBox.appendChild(this.buildDiv(register, 'div'))
    idBigBox.appendChild(this.buildDiv(top, 'div'))

    var kfBox = idBigBox.getElementsByClassName('ys-kf')[0]
    var wxBox = idBigBox.getElementsByClassName('ys-wx')[0]
    var phoneBox = idBigBox.getElementsByClassName('ys-phone')[0]
    var registerBox = idBigBox.getElementsByClassName('ys-register')[0]
    var topBox = idBigBox.getElementsByClassName('ys-top')[0]

    var kfText = this.buildDiv([], 'span')
    var wxText = this.buildDiv([], 'span')
    var phoneText = this.buildDiv([], 'span')
    var phoneNumber = this.buildDiv([{
      attr: 'class',
      value: 'ys-phone-number'
    }], 'div')
    var wxQrcode = this.buildDiv([{
      attr: 'class',
      value: 'ys-wx-qrcode'
    }, {
      attr: 'src',
      value: this.isResp() ? this.respData.wxchat_url : ''
    }], 'img')
    var registerText = this.buildDiv([], 'span')
    var topText = this.buildDiv([], 'span')
    var kfIcon = this.buildDiv([{
      attr: 'class',
      value: 'iconfont icon-xinxi'
    }], 'span')
    var wxIcon = this.buildDiv([{
      attr: 'class',
      value: 'iconfont icon-erweima'
    }], 'span')
    var phoneIcon = this.buildDiv([{
      attr: 'class',
      value: 'iconfont icon-dianhua'
    }], 'span')
    var registerIcon = this.buildDiv([{
      attr: 'class',
      value: 'iconfont icon-zhuce'
    }], 'span')
    var topIcon = this.buildDiv([{
      attr: 'class',
      value: 'iconfont icon-fanhuidingbu'
    }], 'span')

    // 电话号码
    phoneNumber.innerHTML = this.isResp() ? this.respData.phone : ''
    phoneBox.appendChild(phoneNumber)
    wxBox.appendChild(wxQrcode)

    kfText.innerHTML = '在线客服'
    wxText.innerHTML = '添加微信'
    phoneText.innerHTML = '电话号码'
    registerText.innerHTML = '注册账号'
    topText.innerHTML = '返回顶部'
    kfBox.appendChild(kfIcon)
    kfBox.appendChild(kfText)
    wxBox.appendChild(wxIcon)
    wxBox.appendChild(wxText)
    phoneBox.appendChild(phoneIcon)
    phoneBox.appendChild(phoneText)
    registerBox.appendChild(registerIcon)
    registerBox.appendChild(registerText)
    topBox.appendChild(topIcon)
    topBox.appendChild(topText)
  },

  /**
   * 创建元素
   * obj参数为数组对象，attr value
   * ele
   * @return {[type]} [description]
   */
  buildDiv: function(obj, ele) {
    let div = document.createElement(ele)

    for (let i=0; i<obj.length; i++) {
      div.setAttribute(obj[i].attr, obj[i].value)
    }

    return div
  },

  /**
   * 创建新的窗口
   * @param  {[type]} url [description]
   * @return {[type]}     [description]
   */
  newWindow: function(url) {
    var specs = 'directories=0,location=0,menubar=0,scrollbars=0,status=no,toolbar=no,'
    if (this.isPc()) {
      specs += 'width=800,height=600,top=100,left=100'
    } else {
      specs += 'fullscreen=yes'
    }
    window.open(url, '_blank', specs)
  },

  /**
   * 引入css
   * @return {[type]} [description]
   */
  style: function() {
    var link = document.createElement('link')
    link.setAttribute('rel', 'stylesheet')
    link.setAttribute('href', this.domain +'/static/icon/iconfont.css')

    var client = document.createElement('link')
    var css = this.isPc() ? 'client' : 'clienth5'
    client.setAttribute('rel', 'stylesheet')
    client.setAttribute('href', this.domain +'/static/css/'+ css +'.css')

    document.head.appendChild(link)
    document.head.appendChild(client)
  },

  /**
   * 返回顶部
   * @return {[type]} [description]
   */
  handleScrollTop: function() {
    window.scrollTo(0, 0)
  },

  /**
   * 注册跳转
   * @return {[type]} [description]
   */
  handleRegister: function() {
  },

  /**
   * 在线客服点击
   * @return {[type]} [description]
   */
  handleKf: function() {
    let params = '/token-' + this.params.replace(/\./g, "&-") + '-token'
    params += '&host-' + window.location.href + '-host'

    this.newWindow(this.host + params)
  }
}

window.onload = function() {
  client.run()
}
