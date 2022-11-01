'use strict';

class ElementSetItem
{
    constructor(wrap, params)
    {
        this.params = params;
        this.offerWrap = document.getElementById(wrap);
        if (this.offerWrap) {
            this.offerSelectedInput = this.offerWrap.querySelector('[data-offer-selected]');
            this.currentOffer = parseInt(this.params.selectedOffer);
            this.offersCount = this.params.offersCount;
            this.offers = this.params.offers;
            this.matrix = {};

            this.offerProps = this.offerWrap.querySelectorAll('[data-property]');
            this.offerValues = this.offerWrap.querySelectorAll('[data-value]');

            if (this.offerProps) {
                for (let i = 0; i < this.offerProps.length; i++) {
                    this.offerProps[i].onchange = Tool.proxy(this.onOfferChange, this);
                }
            }

            this.selectOffer(
                this.offers[this.currentOffer].selectedTree.property,
                this.offers[this.currentOffer].selectedTree.value
            );
        }
    }

    onOfferChange(event)
    {
        this.selectOffer(
            event.target.getAttribute('data-property'),
            event.target.value
        );

        event.stopPropagation();
    }

    selectOffer(property, value)
    {
        let castOffer, isUpdate, matrix, p, i, k, v, offer;

        property = parseInt(property);
        value = parseInt(value);

        if (property < 1) {
            return 0;
        }

        for (p = 0; p < this.offerValues.length; p++) {
            this.offerValues[p].selected = false;
            this.offerValues[p].classList.remove('selected');
            this.offerValues[p].classList.add('disabled');
        }
        castOffer = this.castOffer(property, value);
        
        document.dispatchEvent(new CustomEvent('select:update', {
            detail: {
              wrap: this.offerWrap
            }
        }));

        isUpdate = this.currentOffer !== castOffer;
        this.currentOffer = castOffer;
        matrix = this.getCanBuyMatrixByPropertyId(property);

        for (i in matrix) {
            if (matrix.hasOwnProperty(i)) {
                if (matrix[i].canBuy === true) {
                    this.offerWrap.querySelector('[data-property="' + property + '"]')
                        .querySelector('[data-value="' + i + '"]').classList.remove('disabled');
                }

                if (parseInt(i) !== value) {
                    continue;
                }

                for (k in matrix[i]) {
                    if (matrix[i].hasOwnProperty(k)) {
                        for (v in matrix[i][k]) {
                            if (matrix[i][k].hasOwnProperty(v)) {
                                if (Tool.inArray(true, matrix[i][k][v])) {
                                    this.offerWrap.querySelector('[data-property="' + k + '"]')
                                        .querySelector('[data-value="' + v + '"]').classList.remove('disabled');
                                }
                            }
                        }
                    }
                }
            }
        }

        this.offerSelectedInput.value = this.offers[this.currentOffer].id;

        if (isUpdate) {
            document.dispatchEvent(new CustomEvent('custom_element_set_offer_change'));
        }
    }

    castOffer(property, value)
    {
        let last, lastIndex, index, temp, i, k, checkedOffer;

        last = 0;
        lastIndex = 0;

        for (i = 0; i < parseInt(this.offersCount); i++) {
            index = 1;

            if (Tool.isEmpty(this.offers[i].tree)) {
                continue;
            }

            for (k in this.offers[this.currentOffer].tree) {
                if (this.offers[this.currentOffer].tree.hasOwnProperty(k)) {
                    temp = parseInt(this.offers[this.currentOffer].tree[k]);
                    k = parseInt(k);

                    if (k === property && parseInt(this.offers[i].tree[k]) === value) {
                        index += 100;
                        continue;
                    }

                    if (k !== property && parseInt(this.offers[i].tree[k]) === temp) {
                        index++;
                    }
                }
            }

            if (index > lastIndex) {
                lastIndex = index;
                last = i;
            }
        }

        for (i in this.offers[last].tree) {
            if (this.offers[last].tree.hasOwnProperty(i)) {
                checkedOffer = this.offerWrap.querySelector('[data-property="' + i + '"]')
                    .querySelector('[data-value="' + this.offers[last].tree[i] + '"]');
                checkedOffer.classList.add('selected');
                checkedOffer.selected = true;
            }
        }

        return last;
    }

    getCanBuyMatrixByPropertyId(property)
    {
        let offer, value, i, k;

        property = parseInt(property);

        if (!Tool.isEmpty(this.matrix[property])) {
            return this.matrix[property];
        }

        for (i = 0; i < parseInt(this.offersCount); i++) {
            offer = this.offers[i];

            if (Tool.isEmpty(offer.tree) || !offer.tree.hasOwnProperty(property)) {
                continue;
            }

            value = parseInt(offer.tree[property]);

            for (k in offer.tree) {
                if (offer.tree.hasOwnProperty(k)) {
                    Tool.makeObjectChain(
                        this.matrix,
                        [property, value, k, parseInt(offer.tree[k])]
                    );

                    this.matrix[property][value][k][parseInt(offer.tree[k])][i] = offer.canBuy;

                    if (offer.canBuy) {
                        this.matrix[property][value]['canBuy'] = true;
                    }
                }
            }
        }

        return this.matrix[property];
    }
}
