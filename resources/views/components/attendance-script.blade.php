@props(['attendanceInstitutionId'])
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceTable', () => ({
            endpoint: @js($attendanceInstitutionId ? route('api.attendance.institution', $attendanceInstitutionId) : null),
            records: [],
            loading: false,
            error: null,
            search: '',
            sortKey: 'occurred_at',
            sortDir: 'desc',
            meta: {
                total: 0
            },

            init() {
                if (this.endpoint) {
                    this.fetchRecords();
                }
            },

            async fetchRecords() {
                if (!this.endpoint) {
                    return;
                }

                this.loading = true;
                this.error = null;

                try {
                    const response = await fetch(`${this.endpoint}?per_page=100`, {
                        headers: {
                            Accept: 'application/json'
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`Request failed with status ${response.status}`);
                    }

                    const payload = await response.json();

                    this.records = payload?.data?.data ?? [];
                    this.meta.total = payload?.data?.total ?? this.records.length;
                } catch (e) {
                    this.error = e?.message ?? 'Failed to load attendance records.';
                    this.records = [];
                } finally {
                    this.loading = false;
                }
            },

            sortBy(key) {
                if (this.sortKey === key) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortKey = key;
                    this.sortDir = 'asc';
                }
            },

            get filtered() {
                const term = this.search.trim().toLowerCase();

                if (!term) {
                    return this.records;
                }

                return this.records.filter((record) => {
                    const haystack = [
                        record.student?.name,
                        record.student?.admission_number,
                        record.student?.student_number,
                        record.device?.name,
                        record.device?.serial_number,
                        record.pin,
                    ].filter(Boolean).join(' ').toLowerCase();

                    return haystack.includes(term);
                });
            },

            get sorted() {
                const dir = this.sortDir === 'asc' ? 1 : -1;
                const key = this.sortKey;

                return [...this.filtered].sort((a, b) => {
                    const va = this.sortValue(a, key);
                    const vb = this.sortValue(b, key);

                    if (va < vb) return -1 * dir;
                    if (va > vb) return 1 * dir;

                    return 0;
                });
            },

            sortValue(record, key) {
                switch (key) {
                    case 'student':
                        return (record.student?.name ?? '').toLowerCase();
                    case 'admission_number':
                        return (record.student?.admission_number ?? '').toLowerCase();
                    case 'device':
                        return (record.device?.name ?? record.device?.serial_number ?? '')
                            .toLowerCase();
                    case 'status':
                        return record.status ?? 0;
                    case 'verify_mode':
                        return record.verify_mode ?? 0;
                    case 'occurred_at':
                    default:
                        return record.occurred_at ? new Date(record.occurred_at).getTime() : 0;
                }
            },

            verifyModeLabel(mode) {
                const labels = {
                    0: 'Password',
                    1: 'Fingerprint',
                    2: 'Card',
                    3: 'Password',
                    4: 'Card',
                    5: 'Fingerprint+Card',
                    6: 'Fingerprint+Password',
                    7: 'Card+Password',
                    8: 'Card+Fingerprint+Password',
                    9: 'Other',
                    15: 'Face',
                    25: 'Palm',
                };

                return labels[mode] ?? `Unknown (${mode})`;
            },

            formatDateTime(value) {
                if (!value) {
                    return '—';
                }

                const date = new Date(value);

                if (Number.isNaN(date.getTime())) {
                    return value;
                }

                return date.toLocaleString();
            },
        }));
    });
</script>
