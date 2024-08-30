export const defaultAgeBrackets = [
    {
        slug: '0-25',
        name: 'Up to 25'
    },
    {
        slug: '26-40',
        name: '26-40'
    },
    {
        slug: '41-55',
        name: '41-55'
    },
    {
        slug: '56+',
        name: '56+'
    },
    {
        slug: 'null',
        name: 'Not Set'
    }
];

export const defaultGenders = [
    {
        slug: 'male',
        name: 'Male'
    },
    {
        slug: 'female',
        name: 'Female'
    },
    {
        slug: 'other',
        name: 'Other'
    },
    {
        slug: 'null',
        name: 'Not Set'
    }
];

export const defaultSubscriptionTypes = [
    {
        slugs: ['standard'],
        name: 'Standard',
        slug: 'standard'
    },
    {
        slugs: ['once-weekly', 'once-weekly-subscription'],
        name: 'Once Weekly',
        slug: 'once-weekly'
    },
    {
        slugs: ['twice-weekly', 'twice-weekly-subscription'],
        name: 'Twice Weekly',
        slug: 'twice-weekly'
    },
    {
        slugs: ['premium', 'premium-membership', 'premium-membership-subscription'],
        name: 'Premium',
        slug: 'premium'
    },
    {
        slugs: ['unlimited-membership', 'unlimited-membership-subscription', 'unlimited-membership-2', 'unlimited-membership-subscription-2'],
        name: 'Unlimited',
        slug: 'unlimited'
    },
    {
        slugs: ['one-month-unlimited'],
        name: '1 Month Unlimited',
        slug: 'one-month-unlimited'
    },
    {
        slugs: ['vip-unlimited', 'vip-unlimited-2'],
        name: 'Unlimited Annual (VIP)',
        slug: 'vip-unlimited'
    }
];

export const defaultExpiredTypes = [
    {
        name: 'Active',
        value: 'active'
    },
    {
        name: 'Expired',
        value: 'expired'
    }
];

export const defaultCreditPackTypes = [
    {
        name: 'Intro Pack',
        ids: [1]
    },
    {
        name: '1 Session',
        ids: [2]
    },
    {
        name: '10 Visits',
        ids: [3, 9]
    },
    {
        name: '30 Visits',
        ids: [4, 10]
    },
    {
        name: '1 Visit for £1',
        ids: [5]
    },
    {
        name: 'Unlimited Promo',
        ids: [11]
    }
];

export const defaultSessionsCompleted = [
    {
        name: 'Any',
        value: 'any'
    },
    {
        name: '0',
        value: '0'
    },
    {
        name: '1',
        value: '1'
    },
    {
        name: '2',
        value: '2'
    },
    {
        name: '3',
        value: '3'
    },
    {
        name: '4',
        value: '4'
    },
    {
        name: '5-10',
        value: '5-10'
    },
    {
        name: '11-20',
        value: '11-20'
    },
    {
        name: '30-50',
        value: '30-50'
    },
    {
        name: '50+',
        value: '50+'
    }
];

export const defaultLastVisitOptions = [
    {
        name: 'All Time / Any',
        value: 'any'
    },
    {
        name: 'No Past Visits',
        value: 'none'
    },
    {
        name: 'Date Range',
        value: 'range'
    }
];

export const defaultNextBookedSessionOptions = [
    {
        name: 'All Time / Any',
        value: 'any'
    },
    {
        name: 'No Future Bookings',
        value: 'none'
    },
    {
        name: 'Date Range',
        value: 'range'
    }
];

export const defaultAverageVisitOptions = [
    {
        name: 'Any',
        value: 'any'
    },
    {
        name: '< 1',
        value: 1,
    },
    {
        name: '1-2',
        value: '1-2',
    },
    {
        name: '2+',
        value: 2,
    }
];

export const defaultTotalCreditOptions = [
    {
        name: 'Any',
        value: 'any'
    },
    {
        name: '0',
        value: 0
    },
    {
        name: '1',
        value: 1
    },
    {
        name: '2-4',
        value: '2-4'
    },
    {
        name: '5+',
        value: 5
    }
];

export const defaultGuestStatusOptions = [
    {
        name: 'Prospect',
        value: 'Prospect',

    },
    {
        name: 'Active Guest',
        value: 'Active Guest',
    },
    {
        name: 'Idle Guest',
        value: 'Idle Guest',
    },
    {
        name: 'Lapsed Guest',
        value: 'Lapsed Guest',
    }
];

export const defaultParqStatuses = [
    {
        name: 'Completed',
        value: 'completed',
    },
    {
        name: 'Not Completed',
        value: 'not-completed',
    }
];

export const defaultSubscriptionStatuses = [
    {
        name: 'Active - Renews',
        value: 'active-renews'
    },
    {
        name: 'Active - Does Not Renew',
        value: 'active-does-not-renew'
    },
    {
        name: 'Expired',
        value: 'expired'
    },
    {
        name: 'Deleted',
        value: 'Deleted'
    }
];


export const defaultLastCallDateOptions = [
    {
        name: 'All Time / Any',
        value: 'any'
    },
    {
        name: 'No calls',
        value: 'none'
    },
    {
        name: 'Date Range',
        value: 'range'
    }
];

export const defaultLastEmailDateOptions = [
    {
        name: 'All Time / Any',
        value: 'any'
    },
    {
        name: 'No emails',
        value: 'none'
    },
    {
        name: 'Date Range',
        value: 'range'
    }
];

export const defaultAppointmentDateOptions = [
    {
        name: 'All Time / Any',
        value: 'any'
    },
    {
        name: 'No Future Bookings',
        value: 'none'
    },
    {
        name: 'Date Range',
        value: 'range'
    }
];


export const defaultLifetimeValueOptions = [
    {
        name: 'None',
        value: 'none'
    },
    {
        name: '£1 - £50',
        value: '100-5000'
    },
    {
        name: '> £50',
        value: '5100-1000000000000'
    }
];

export const defaultNumberOfCallsMadeOptions = [
    {
        name: '0',
        value: '0-0'
    },
    {
        name: '1',
        value: '1-1'
    },
    {
        name: '2',
        value: '2-2'
    },
    {
        name: '3',
        value: '3-3'
    },
    {
        name: '4',
        value: '4-4'
    },
    {
        name: '5-10',
        value: '5-10'
    },
    {
        name: '>10',
        value: '11-10000000'
    }
];

export const defaultNumberOfEmailsSentOptions = [
    {
        name: '0',
        value: '0-0'
    },
    {
        name: '1',
        value: '1-1'
    },
    {
        name: '2',
        value: '2-2'
    },
    {
        name: '3',
        value: '3-3'
    },
    {
        name: '4',
        value: '4-4'
    },
    {
        name: '5-10',
        value: '5-10'
    },
    {
        name: '>10',
        value: '11-10000000'
    }
];

export default {
}
