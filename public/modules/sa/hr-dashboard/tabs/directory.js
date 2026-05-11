window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.directory = {
    init: function () {
        const config = window.HRDashboardConfig || {};
        const loadUrl = config.directoryLoadUrl || '';
        const listDataUrl = config.directoryListDataUrl || '';

        const searchInput = document.getElementById('hrxDirectorySearch');
        const departmentSelect = document.getElementById('hrxDirectoryDepartment');
        const statusSelect = document.getElementById('hrxDirectoryStatus');
        const gridToggleBtn = document.getElementById('hrxDirectoryGridToggle');
        const listToggleBtn = document.getElementById('hrxDirectoryListToggle');
        const gridSection = document.getElementById('hrxDirectoryGridSection');
        const listSection = document.getElementById('hrxDirectoryListSection');
        const loadMoreBtn = document.getElementById('hrxDirectoryLoadMore');
        const counterEl = document.getElementById('hrxDirectoryCounter');
        const cardsWrap = document.getElementById('hrxDirectoryCards');

        if (!loadUrl || !cardsWrap) {
            return;
        }

        let isLoading = false;
        let debounceTimer = null;
        let activeView = 'grid';
        let listDataTable = null;
        let listLoadedOnce = false;
        let gridHasMore = loadMoreBtn ? loadMoreBtn.style.display !== 'none' : false;
        const loadMoreDefaultHtml = loadMoreBtn ? loadMoreBtn.innerHTML : '';

        function setLoadingState(loading) {
            if (!loadMoreBtn) {
                return;
            }

            loadMoreBtn.disabled = loading;
            loadMoreBtn.innerHTML = loading
                ? '<i class="fa fa-spinner fa-spin"></i>Loading...'
                : loadMoreDefaultHtml;
        }

        function updateCounter(shownCount, totalCount) {
            if (!counterEl) {
                return;
            }

            const shown = Number(shownCount || 0);
            const total = Number(totalCount || 0);
            counterEl.textContent = 'Showing ' + shown + ' of ' + total + ' staff';
        }

        function getFilters() {
            return {
                search: (searchInput ? searchInput.value : '').trim(),
                department: (departmentSelect ? departmentSelect.value : '').trim(),
                status: (statusSelect ? statusSelect.value : '').trim()
            };
        }

        function setLoadMoreState(hasMore, nextPage) {
            gridHasMore = Boolean(hasMore);

            if (!loadMoreBtn) {
                return;
            }

            if (!gridHasMore || activeView !== 'grid') {
                loadMoreBtn.style.display = 'none';
                return;
            }

            loadMoreBtn.style.display = '';
            loadMoreBtn.dataset.nextPage = String(nextPage || 2);
        }

        function fetchPage(page, appendMode) {
            if (activeView !== 'grid') {
                return;
            }

            if (isLoading) {
                return;
            }

            isLoading = true;
            setLoadingState(true);

            const filters = getFilters();

            $.ajax({
                url: loadUrl,
                type: 'GET',
                data: {
                    page: page,
                    search: filters.search,
                    department: filters.department,
                    status: filters.status
                },
                success: function (response) {
                    if (!response || !response.status) {
                        return;
                    }

                    if (appendMode) {
                        cardsWrap.insertAdjacentHTML('beforeend', response.cards_html || '');
                    } else {
                        cardsWrap.innerHTML = response.cards_html || '<div class="hrx-loading">No staff records found.</div>';
                    }

                    setLoadMoreState(Boolean(response.has_more), response.next_page);
                    updateCounter(response.shown_count, response.total_count);
                },
                error: function () {
                    if (typeof sendmsg === 'function') {
                        sendmsg('error', 'Unable to load staff directory right now.');
                    }
                },
                complete: function () {
                    isLoading = false;
                    setLoadingState(false);
                }
            });
        }

        function resetAndLoad() {
            if (activeView === 'grid') {
                fetchPage(1, false);
                return;
            }

            if (listDataTable) {
                listDataTable.ajax.reload();
            } else {
                initListDataTable();
            }
        }

        function initListDataTable() {
            if (listDataTable || !listDataUrl) {
                return;
            }

            const hasUpperApi = typeof $.fn.DataTable === 'function';
            const hasLowerApi = typeof $.fn.dataTable === 'function';

            if (!hasUpperApi && !hasLowerApi) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'DataTable library not available for List View.');
                }
                return;
            }

            const tableOptions = {
                processing: true,
                serverSide: true,
                paging: true,
                info: true,
                searching: false,
                lengthChange: true,
                responsive: true,
                autoWidth: true,
                dom: 'Blrtip',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                buttons: [
                    {
                        text: '<i class="fa fa-sync"></i>',
                        className: 'btn btn-secondary',
                        titleAttr: 'Reload Table',
                        action: function (e, dt) {
                            dt.ajax.reload();
                        }
                    },
                    { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                    { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
                    { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
                    { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
                    { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' },
                    { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
                ],
                ajax: {
                    url: listDataUrl,
                    type: 'GET',
                    data: function (d) {
                        const filters = getFilters();
                        d.search_filter = filters.search;
                        d.department_filter = filters.department;
                        d.status_filter = filters.status;
                    }
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'name', name: 'name', orderable: true, searchable: false },
                    { data: 'department', name: 'department' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'join_date', name: 'join_date' },
                    { data: 'status', name: 'status', orderable: true, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { targets: [1, 6, 7], orderable: false }
                ],
                drawCallback: function () {
                    listLoadedOnce = true;
                    const wrapper = document.getElementById('hrxDirectoryListTable_wrapper');
                    if (wrapper) {
                        const paginate = wrapper.querySelector('.dataTables_paginate, .dt-paging');
                        if (paginate) {
                            paginate.style.float = 'none';
                            paginate.style.textAlign = 'left';
                        }

                        const paginateCell = wrapper.querySelector('.dt-layout-end');
                        if (paginateCell) {
                            paginateCell.style.justifyContent = 'flex-start';
                        }
                    }
                }
            };

            listDataTable = hasUpperApi
                ? $('#hrxDirectoryListTable').DataTable(tableOptions)
                : $('#hrxDirectoryListTable').dataTable(tableOptions).api();

            $('#hrxDirectoryListTable')
                .off('preXhr.dt.hrxdir xhr.dt.hrxdir error.dt.hrxdir')
                .on('preXhr.dt.hrxdir', function () {
                    if (typeof loader === 'function') {
                        loader('show');
                    }
                })
                .on('xhr.dt.hrxdir error.dt.hrxdir', function () {
                    if (typeof loader === 'function') {
                        loader('hide');
                    }
                });
        }

        function setActiveView(view, forceReload) {
            activeView = view === 'list' ? 'list' : 'grid';

            if (gridSection) {
                gridSection.style.display = activeView === 'grid' ? '' : 'none';
            }

            if (listSection) {
                listSection.style.display = activeView === 'list' ? '' : 'none';
            }

            if (gridToggleBtn) {
                gridToggleBtn.classList.toggle('active', activeView === 'grid');
            }

            if (listToggleBtn) {
                listToggleBtn.classList.toggle('active', activeView === 'list');
            }

            if (loadMoreBtn) {
                loadMoreBtn.style.display = activeView === 'grid' && gridHasMore ? '' : 'none';
            }

            if (counterEl) {
                counterEl.style.display = activeView === 'grid' ? '' : 'none';
            }

            if (activeView === 'list') {
                if (!listLoadedOnce) {
                    initListDataTable();
                } else if (listDataTable) {
                    listDataTable.ajax.reload();
                }

                if (listDataTable && typeof listDataTable.columns === 'function') {
                    listDataTable.columns.adjust();
                }
            } else if (forceReload) {
                fetchPage(1, false);
            }
        }

        [searchInput, departmentSelect, statusSelect].forEach(function (el) {
            if (!el) {
                return;
            }
            el.addEventListener('change', resetAndLoad);
            el.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(resetAndLoad, 300);
            });
        });

        if (gridToggleBtn) {
            gridToggleBtn.addEventListener('click', function () {
                setActiveView('grid', false);
            });
        }

        if (listToggleBtn) {
            listToggleBtn.addEventListener('click', function () {
                setActiveView('list', false);
            });
        }

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                const nextPage = parseInt(loadMoreBtn.dataset.nextPage || '2', 10);
                fetchPage(nextPage, true);
            });
        }

        setLoadMoreState(loadMoreBtn && loadMoreBtn.style.display !== 'none', parseInt(loadMoreBtn ? loadMoreBtn.dataset.nextPage || '2' : '2', 10));
        setActiveView('grid', false);
        return true;
    }
};
