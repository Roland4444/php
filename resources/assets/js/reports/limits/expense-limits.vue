<script>
    export default {
        props: {
            data: {
                type: Array,
                required: true,
            },
            apiUrl: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                blocks: this.data,
                selectedBlock: -1,
                showAddBlockForm: false,
                addItem: {
                    title: '',
                    money: '',
                },
                blockName: '',
                blockEdit: null,
                commentEdit: null,
            };
        },
        methods: {
            removeItem(blockIndex, itemIndex) {
                this.blocks[blockIndex].items.splice(itemIndex, 1);
                this.saveData();
            },
            btnShowItemForm(blockIndex) {
                this.selectedBlock = blockIndex;
                this.$nextTick(() => {
                    this.$refs[`title${blockIndex}`][0].focus();
                });
            },
            btnSaveItem() {
                const blockIndex = this.selectedBlock;
                const item = {};
                this.blocks[blockIndex].items.push(Object.assign(item, this.addItem));
                this.addItem.title = '';
                this.addItem.money = '';
                this.selectedBlock = -1;
                this.saveData();
            },
            btnCancelItem() {
                this.selectedBlock = -1;
            },
            btnShowBlockFrom() {
                this.showAddBlockForm = true;
                this.$nextTick(() => {
                    this.$refs.blockNameInput.focus();
                });
            },
            btnSaveBlock() {
                this.blocks.push({
                    headline: this.blockName,
                    items: [],
                    comment: '',
                });
                this.blockName = '';
                this.showAddBlockForm = false;
                this.saveData();
            },
            onUpdateBlockName() {
                this.blockEdit = null;
                this.saveData();
            },
            btnCancelBlock() {
                this.showAddBlockForm = false;
            },
            removeBlock(blockIndex) {
                this.blocks.splice(blockIndex, 1);
                this.saveData();
            },
            clickEditBlock(blockIndex) {
                this.blockEdit = blockIndex;
                this.$nextTick(() => {
                    this.$refs[`blockEditInput${blockIndex}`][0].focus();
                });
            },
            clickEditComment(blockIndex) {
                this.commentEdit = blockIndex;
                this.$nextTick(() => {
                    this.$refs[`commentEditInput${blockIndex}`][0].focus();
                });
            },
            onUpdateBlockComment() {
                this.commentEdit = null;
                this.saveData();
            },
            saveData() {
                this.$http.post(this.apiUrl, this.blocks).then(
                    () => { },
                    () => {
                        this.$notify({
                            title: 'Ошибка',
                            text: 'Не удалось сохранить данные',
                            type: 'error',
                        });
                    },
                );
            },
        },
    };
</script>
<template>
    <div>
        <notifications />

        <button
            style="margin-bottom: 20px;"
            type="button"
            class="btn btn-default btn-sm"
            @click="btnShowBlockFrom"
        >
            <span class="glyphicon glyphicon-plus" />
            Добавить раздел
        </button>
        <div
            v-if="showAddBlockForm"
            style="margin-top: 10px;"
        >
            <input
                ref="blockNameInput"
                v-model="blockName"
                type="text"
            >
            <button
                type="button"
                class="btn btn-default btn-sm"
                @click="btnSaveBlock"
            >
                <span class="glyphicon glyphicon-ok" />
            </button>
            <button
                type="button"
                class="btn btn-default btn-sm"
                @click="btnCancelBlock"
            >
                <span class="glyphicon glyphicon-remove" />
            </button>
        </div>

        <div
            v-for="(block, blockIndex) in blocks"
            :key="blockIndex"
        >
            <h2>
                <input
                    v-if="blockEdit === blockIndex"
                    :ref="'blockEditInput' + blockIndex"
                    v-model="block.headline"
                    @blur="onUpdateBlockName"
                    @keyup.enter="onUpdateBlockName"
                >
                <div v-else>
                    <label @click="clickEditBlock(blockIndex)">
                        {{ block.headline }}
                    </label>
                    &nbsp;
                    <button
                        type="button"
                        class="btn btn-default btn-sm"
                        @click="removeBlock(blockIndex)"
                    >
                        <span class="glyphicon glyphicon-remove" />
                    </button>
                </div>
            </h2>
            <div>
                <button
                    type="button"
                    class="btn btn-default btn-sm"
                    @click="btnShowItemForm(blockIndex)"
                >
                    <span class="glyphicon glyphicon-plus" />
                    Добавить
                </button>
            </div>
            <div
                v-if="selectedBlock === blockIndex"
                style="margin-top: 10px;"
            >
                <input
                    :ref="'title' + blockIndex"
                    v-model="addItem.title"
                    type="text"
                >
                <input
                    ref="money"
                    v-model="addItem.money"
                    type="text"
                >
                <button
                    type="button"
                    class="btn btn-default btn-sm"
                    @click="btnSaveItem"
                >
                    <span class="glyphicon glyphicon-ok" />
                </button>
                <button
                    type="button"
                    class="btn btn-default btn-sm"
                    @click="btnCancelItem"
                >
                    <span class="glyphicon glyphicon-remove" />
                </button>
            </div>
            <table
                style="width: 500px;"
                class="table table-striped main-table"
            >
                <tbody>
                    <tr
                        v-for="(item, itemIndex) in block.items"
                        :key="itemIndex"
                    >
                        <td>
                            {{ item.title }}
                        </td>
                        <td class="num">
                            {{ item.money }}
                        </td>
                        <td class="action">
                            <button
                                type="button"
                                class="btn btn-default btn-sm"
                                @click="removeItem(blockIndex, itemIndex)"
                            >
                                <span class="glyphicon glyphicon-remove" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input
                v-if="commentEdit === blockIndex"
                :ref="'commentEditInput' + blockIndex"
                v-model="block.comment"
                @blur="onUpdateBlockComment"
                @keyup.enter="onUpdateBlockComment"
            >
            <div v-else>
                <label @click="clickEditComment(blockIndex)">
                    <em>
                        {{ block.comment || 'добавить комментарий' }}
                    </em>
                </label>
                &nbsp;
            </div>
        </div>
    </div>
</template>
