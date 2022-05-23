import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['collection', 'items', 'typeField'];

    connect() {
        this.proto = this.element.dataset.prototype;
        this.number = this.itemsTargets.length;
    }

    addItem() {
        const proto = this.proto.replaceAll('__name__', this.number);
        this.collectionTarget.insertAdjacentHTML('beforeend', proto);
        this.number++;
        Admin.setup_icheck(this.collectionTarget)
        this.computePosition();
    }

    deleteItem(e) {
        const item = e.currentTarget.closest('[data-actuality-media-collection-target="items"]');
        item.remove();
        this.computePosition();
    }

    moveUp(e) {
        const line = e.currentTarget.closest('[data-actuality-media-collection-target="items"]');
        const previousLine = line.previousElementSibling;
        previousLine.insertAdjacentElement('beforebegin', line);
        this.computePosition();
    }

    moveDown(e) {
        const line = e.currentTarget.closest('[data-actuality-media-collection-target="items"]');
        const nextLine = line.nextElementSibling;
        nextLine.insertAdjacentElement('afterend', line);
        this.computePosition();
    }

    computePosition() {
        this.itemsTargets.forEach((item, key) => {
            const linePosition = item.querySelector('[data-actuality-media-collection-target="positionField"]');
            linePosition.value = key;
        })
    }
}