export const httpRequestWrapperMixin = {
    methods: {
        httpPostRequest(url, data, muteNotifications = false) {
            this.waitWhileSaving = true;

            return new Promise((resolve, reject) => {
                this.$http.post(url, data).then((response) => {
                    if (!muteNotifications) {
                        this.$notify({
                            title: 'Успешно',
                            text: 'Запрос выполнен успешно',
                        });
                    }
                    this.waitWhileSaving = false;
                    resolve(response);
                }, (response) => {
                    const error = this.getErrorMessage(response);
                    this.$notify({
                        title: 'Ошибка',
                        text: error,
                        type: 'error',
                    });
                    this.waitWhileSaving = false;
                    reject(error);
                });
            });
        },
        httpGetRequest(url, data = {}) {
            return new Promise((resolve, reject) => {
                this.$http.get(url, data).then((response) => {
                    resolve(response);
                }, (response) => {
                    this.$notify({
                        title: 'Ошибка',
                        text: this.getErrorMessage(response),
                        type: 'error',
                    });
                    reject(response);
                });
            });
        },

        getErrorMessage(response) {
            if (response.body.error.message) {
                return response.body.error.message;
            }
            if (response.body.error) {
                return response.body.error;
            }
            return 'При выполнении запроса произошла ошибка';
        },
    },
};
export default 'httpRequestWrapperMixin';
