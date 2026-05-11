XDir = function(path) {
    this.data = {};
    this.path = path;
    this.xdir_url = 'x_dir_list.php';
    this.expand_icon = '<img src="/test/img/icn_plus.gif" />';
    this.close_icon = '<img src="/test/img/icn_minus.gif" />';
    this.leaf_icon = '<img src="/test/img/icn_nosub.gif" />';
    this.tpls = {
        'close_head': new Template('<a onclick="xdir.close(\'#{id}\')">#{icon}</a> <a href="javascript:dir(#{id})">#{name}</a>'),
        'expand_head': new Template('<a onclick="xdir.dir(\'#{id}\')">#{icon}</a> <a href="javascript:dir(#{id})">#{name}</a>'),
        'trank_node': new Template('<div id="dir_head_#{id}" style="margin-left:10px"><a onclick="xdir.dir(\'#{id}\')">#{icon}</a> <a href="javascript:dir(#{id})">#{name}</a></div><div id="dir_box_#{id}" style="margin-left:10px"><img height="1" /></div>'),
        'leaf_node': new Template('<div id="dir_head_#{id}" style="margin-left:10px">#{icon} <a href="javascript:dir(#{id})">#{name}</a></div><div style="margin-left:10px"><img height="1" /></div>')
    }
}
XDir.prototype = {
    init: function() {
        var id = this.path.shift();
        if(id == undefined) return;
        if(id != '0') {
            if($('dir_head_'+id) == null) return;
            $('dir_head_'+id).innerHTML = 'Loading...';
        }
        if(this.data[id] == undefined) {
            var o_xdir = this;
            new Ajax.Request(this.xdir_url, {
                method: 'get',
                encoding: '',
                parameters: {'id': id},
                onSuccess: function(transport) {
                    o_xdir.data[id] = transport.responseText.evalJSON(true);
                    o_xdir.expand(id);
                    o_xdir.init();
                }
            });
        }
        else {
            this.expand(id);
            this.init();
        }
    },
    dir: function(id) {
        if(id != '0') {
            if($('dir_head_'+id) == null) return;
            $('dir_head_'+id).innerHTML = 'Loading...';
        }
        if(this.data[id] == undefined) {
            var o_xdir = this;
            new Ajax.Request(this.xdir_url, {
                method: 'get',
                encoding: '',
                parameters: {'id': id},
                onSuccess: function(transport) {
                    o_xdir.data[id] = transport.responseText.evalJSON(true);
                    o_xdir.expand(id);
                }
            });
        }
        else {
            this.expand(id);
        }
    },

    expand: function(id) {
        var str = '';
        var list = this.data[id]['subs'];
        var name = this.data[id]['name'];
        for(var i=0; i<list.length; i++) {
            str += this.get_node_str(list[i]);
        }
        if($('dir_box_' + id) == null) return;
        $('dir_box_' + id).innerHTML = str;
        if(id != '0') $('dir_head_' + id).innerHTML = this.get_close_head_str(id, name);
    },
    
    close: function(id) {
        var name = this.data[id]['name'];
        $('dir_head_' + id).innerHTML = this.get_expand_head_str(id, name);
        $('dir_box_' + id).innerHTML = '<img height="1" />';
    },
    
    get_close_head_str: function(id, name) {
        return this.tpls.close_head.evaluate({'id':id,'icon': this.close_icon,'name':name}); 
    },
    
    get_expand_head_str: function(id, name) {
        return this.tpls.expand_head.evaluate({'id':id,'icon': this.expand_icon,'name':name}); 
    },
    
    get_node_str: function(dir) {
        var s_tpl = '';
        var s_node = '';
        var name = (dir.type=='2') ? dir.name + '@' : dir.name;
        var icon = (parseInt(dir.child_cnt) > 0) ? this.expand_icon : this.leaf_icon;
        var tpl_data = {'id':dir.id, 'name':name, 'icon': icon};
        if(parseInt(dir.child_cnt) > 0) {
            return this.tpls.trank_node.evaluate(tpl_data); 
        }
        return this.tpls.leaf_node.evaluate(tpl_data); 
    }
}


function dir(id) {
    alert('카테고리 id(' + id + ') 구현 페이지로 이동');
}