var timeOut;

class Item {
    constructor(icon, backgroundColor, link, label) {
        this.$element = $(document.createElement("a"));
        this.icon = icon;
        this.$element.addClass("item");
        this.$element.css("background-color", backgroundColor);
        this.$element.attr('href', link);
        this.$element.attr('data-toggle', 'tooltip');
        this.$element.attr('title', label);
        var i = document.createElement("i");
        $(i).addClass(icon);
        this.$element.append(i);
        this.prev = null;
        this.next = null;
        this.isMoving = false;
        var element = this;
    }

    updatePosition() {
        anime({
            targets: this.$element[0],
            left: this.prev.$element.css("left"),
            top: this.prev.$element.css("top"),
            duration: 200
        });

        if (this.next) {
            this.next.updatePosition();
        }
    }
}

class Menu {
    constructor(menu) {
        this.$element = $(menu);
        this.size = 0;
        this.first = null;
        this.last = null;
        this.timeOut = null;
        this.hasMoved = false;
        this.status = "closed";
    }

    add(item) {
        var menu = this;
        if (this.first == null) {
            this.first = item;
            this.last = item;
            this.first.$element.on("mouseup", function() {
                if (menu.first.isMoving) {
                    menu.first.isMoving = false;
                } else {
                    menu.click();
                }
            });
        } else {
            this.last.next = item;
            item.prev = this.last;
            this.last = item;
        }
        this.$element.after(item.$element);


    }

    open() {
        this.status = "open";
        var current = this.first.next;
        var iterator = 1;
        var head = this.first;
        var sens = head.$element.css("bottom") < head.$element.css("top") ? 1 : -1;
        while (current != null) {
            anime({
                targets: current.$element[0],
                top: parseInt(head.$element.css("bottom"), 10) + (sens * (iterator * 75)),
                //top: head.$element.css("top"),
                duration: 500
            });
            iterator++;
            current = current.next;
        }
    }

    close() {
        this.status = "closed";
        var current = this.first.next;
        var head = this.first;
        var iterator = 1;
        while (current != null) {
            anime({
                targets: current.$element[0],
                top: head.$element.css("left"),
                //top: head.$element.css("top"),
                duration: 500
            });
            iterator++;
            current = current.next;
        }
    }

    click() {
        if (this.status == "closed") {
            this.open();
        } else {
            this.close();
        }
    }

}