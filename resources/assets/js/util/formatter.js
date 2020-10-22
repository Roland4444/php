export const formatPrice = (price) => {
    const formatter = new Intl.NumberFormat('ru', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2,
    });

    return formatter.format(price);
};

export const formatWeight = (weight) => new Intl.NumberFormat('ru', {
        style: 'decimal',
        minimumFractionDigits: 2,
    }).format(weight);

export const roundNumber = (number) => Math.round(number * 1e2) / 1e2;
