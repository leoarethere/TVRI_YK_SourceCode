let apexChartsPromise = null;

window.loadApexCharts = () => {
    if (!apexChartsPromise) {
        apexChartsPromise = import('apexcharts')
            .then(({ default: ApexCharts }) => ApexCharts)
            .catch((error) => {
                apexChartsPromise = null;
                throw error;
            });
    }

    return apexChartsPromise;
};
