<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--member">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Reports
                    </h1>

                    <h2 class="page-header__sub">All Reports</h2>
                </div>
            </div>
        </section>

        <section>
            <div class="page-content page-content--small-top">
                <div class="table-list table-list--top">
                    <div class="table-list__scroll">
                        <table class="table-list__table">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Category</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td><router-link to="/admin/reports/new-sign-ups">New Sign Ups</router-link></td>
                                    <td>Users</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    }
}
</script>
