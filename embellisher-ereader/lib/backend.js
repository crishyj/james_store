define([], function() {
    //backend server

    var returnedModule = function() {
        //ENTER THE MAIN URL HERE
        this.application = "http://james.com/embellisher-ereader/";

        this.mainurl = this.application + "api/";
        this.shareurl = this.application + "?bookid=";
        this.forum = "toforumi.php";
        this.checkcoupon = "checkcoupon.php";
        this.showurl = "request.php";
        this.loginurl = "login.php";
        this.registerurl = "register.php";
        this.logouturl = "logout.php";
        this.getlibrary = "getlibrary.php";
        this.getstore = "getstore.php";
        this.buybook = "buybook.php";
        this.addbook = "addbook.php";
        this.deletebook = "deletebook.php";
        this.editprofileurl = "editprofile.php";
        //Switch to false for only one store
        this.separate_admins = true;

        //ENTER YOUR STRIPE PUBLIC KEY HERE
        //this.stripeKey = "pk_live_rfOXDfiSYjtewlhbEk68hBmE";
        //console.log(localStorage.getItem('backend_userInfo'));
        if (localStorage.getItem('backend_userInfo') != 'undefined') {
            this.userInfo = JSON.parse(localStorage.getItem('backend_userInfo'));
        }
        if (!this.userInfo) {
            this.userInfo = {
                loggedin: 0,
                name: "",
                type: "Reader",
                email: "",
                genre_of_writing: "",
                status: "",
                public_private: "",
                interests: "",
                storeid: 0,
                sessionid: ""
            };
        }
        this.saveUserInfo = function(userinfo) {
            this.userInfo = userinfo;
            //console.log("saving"+userinfo);
            localStorage.setItem('backend_userInfo', JSON.stringify(userinfo));
        }
        this.getUserInfo = function() {
            return this.userInfo;
        }
    };

    return returnedModule;
});