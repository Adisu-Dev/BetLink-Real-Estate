import api, { cachedGet } from './api'

/**
 * Helper to download binary or text Blob data as a file
 */
export function downloadBlob(data, filename, mimeType = 'text/csv;charset=utf-8;') {
  if (data === null || data === undefined || data === 'undefined') {
    console.error('Download cancelled: data is empty or undefined')
    return null
  }
  const blob = data instanceof Blob ? data : new Blob([data], { type: mimeType })
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.style.display = 'none'
  link.href = url
  link.download = filename
  link.setAttribute('download', filename)
  link.setAttribute('target', '_self')
  document.body.appendChild(link)

  // Dispatch click event
  try {
    link.click()
  } catch (e) {
    const event = new MouseEvent('click', { bubbles: true, cancelable: true, view: window })
    link.dispatchEvent(event)
  }

  // Defer removal so the browser keeps the download attribute and file extension intact
  setTimeout(() => {
    try {
      if (link.parentNode) {
        link.parentNode.removeChild(link)
      }
    } catch (err) {}
    setTimeout(() => {
      window.URL.revokeObjectURL(url)
    }, 10000)
  }, 1500)

  return url
}

/**
 * Clean phone formatter helper
 */
export function formatReportPhone(phone) {
  if (!phone || phone === '—' || phone === 'null' || phone === 'undefined') return '—'
  const raw = String(phone).trim().replace(/[\s-]/g, '')
  if (raw.startsWith('+251')) {
    const rest = raw.substring(4)
    if (rest.length >= 8) {
      return `+251 ${rest.substring(0, 2)} ${rest.substring(2, 5)} ${rest.substring(5)}`
    }
    return raw
  }
  if (raw.startsWith('251')) {
    const rest = raw.substring(3)
    if (rest.length >= 8) {
      return `+251 ${rest.substring(0, 2)} ${rest.substring(2, 5)} ${rest.substring(5)}`
    }
    return `+${raw}`
  }
  if ((raw.startsWith('09') || raw.startsWith('07')) && raw.length === 10) {
    return `${raw.substring(0, 4)} ${raw.substring(4, 7)} ${raw.substring(7)}`
  }
  return raw
}

/**
 * Clean date formatter helper
 */
export function formatReportDate(dateStr) {
  if (!dateStr || dateStr === '—') return '—'
  try {
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return String(dateStr)
    return d.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return String(dateStr)
  }
}

/**
 * Generate high-end Excel spreadsheet (.xls) with full styling, auto column widths,
 * and text-protected number formats (never scientific 2.519E+11, never #########).
 */
export function generateClientExcel({ title, subtitle, headers = [], rows = [], filename }) {
  const safeFilename = (filename || `BetLink_Report_${Date.now()}`)
    .replace(/\.csv$/, '')
    .replace(/\.xls[x]?$/, '') + '.xls'

  const escapeXml = (val) => {
    if (val === null || val === undefined) return ''
    return String(val)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&apos;')
  }

  // Calculate generous column widths so WPS Office and Microsoft Excel NEVER clip text
  const colWidths = headers.map(h => {
    const s = String(h).toLowerCase()
    if (s.includes('id') || s === 'no' || s === '#') return 55
    if (s.includes('title') || s.includes('target') || s.includes('property')) return 240
    if (s.includes('email')) return 220
    if (s.includes('name') || s.includes('owner') || s.includes('reporter')) return 170
    if (s.includes('phone') || s.includes('contact')) return 160
    if (s.includes('date') || s.includes('time') || s.includes('scheduled')) return 170
    if (s.includes('price')) return 130
    if (s.includes('type') || s.includes('category') || s.includes('reason')) return 120
    if (s.includes('status')) return 100
    if (s.includes('description')) return 280
    return 140
  })

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Title">
   <Font ss:FontName="Segoe UI" ss:Size="14" ss:Bold="1" ss:Color="#0F172A"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Subtitle">
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Italic="1" ss:Color="#64748B"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#0F172A" ss:Pattern="Solid"/>
   <Alignment ss:Vertical="Center" ss:Horizontal="Left"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
   </Borders>
  </Style>
  <Style ss:ID="DataCell">
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
   <Alignment ss:Vertical="Center" ss:Horizontal="Left"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <NumberFormat ss:Format="@"/>
  </Style>
  <Style ss:ID="DataCellAlt">
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
   <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
   <Alignment ss:Vertical="Center" ss:Horizontal="Left"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <NumberFormat ss:Format="@"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Report Data">
  <Table ss:DefaultRowHeight="20">
   ${colWidths.map(w => `<Column ss:Width="${w}"/>`).join('\n   ')}
   <Row ss:Height="24">
    <Cell ss:StyleID="Title"><Data ss:Type="String">${escapeXml(title || 'BetLink Report')}</Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="Subtitle"><Data ss:Type="String">${escapeXml((subtitle || '') + ' | Exported on ' + new Date().toLocaleString())}</Data></Cell>
   </Row>
   <Row ss:Height="8"/>
   <Row ss:Height="22">
    ${headers.map(h => `<Cell ss:StyleID="Header"><Data ss:Type="String">${escapeXml(h)}</Data></Cell>`).join('\n    ')}
   </Row>
   ${rows.map((row, idx) => `
   <Row ss:Height="20">
    ${row.map(cell => `<Cell ss:StyleID="${idx % 2 === 0 ? 'DataCell' : 'DataCellAlt'}"><Data ss:Type="String">${escapeXml(cell)}</Data></Cell>`).join('\n    ')}
   </Row>`).join('')}
  </Table>
 </Worksheet>
</Workbook>`

  const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' })
  downloadBlob(blob, safeFilename, 'application/vnd.ms-excel;charset=utf-8;')
}

/**
 * Generate CSV client-side strictly for clean table listings with phone protection
 */
export function generateClientCsv(rows, filename, customHeaders = null) {
  if (!rows || !rows.length) {
    console.warn('generateClientCsv received empty rows')
    return
  }
  const keys = customHeaders ? Object.keys(customHeaders) : Object.keys(rows[0])
  const headerLabels = customHeaders ? Object.values(customHeaders) : keys

  const csvRows = [
    headerLabels.join(','),
    ...rows.map(row => 
      keys.map(field => {
        let val = row[field] === null || row[field] === undefined ? '' : String(row[field])
        if (val.startsWith('+') || (val.startsWith('09') && val.length >= 10)) {
          val = `\t${val}`
        }
        if (val.includes(',') || val.includes('"') || val.includes('\n') || val.includes('\r')) {
          val = `"${val.replace(/"/g, '""')}"`
        }
        return val
      }).join(',')
    )
  ]

  const csvContent = csvRows.join('\r\n')
  downloadBlob('\uFEFF' + csvContent, filename, 'text/csv;charset=utf-8;')
}

function getClientColumnWidths(headers) {
  const count = headers.length
  const headerStr = headers.map(h => String(h).toLowerCase()).join('|')

  // 1. Agent Leads (6 columns): ['No', 'Client Name', 'Inquired Property', 'Listing Type', 'Pipeline Status', 'Date']
  if (headerStr.includes('pipeline status') && count === 6) {
    return [25, 115, 145, 75, 85, 70] // sum = 515
  }

  // 2. Agent Leads (4 columns fallback without No / Listing Type)
  if (headerStr.includes('pipeline status') && count === 4) {
    return [125, 170, 110, 110] // sum = 515
  }

  // 3. Agent Managed Listings (6 columns): ['No', 'Property Title', 'Type', 'Price (ETB)', 'Views', 'Status']
  if (headerStr.includes('property title') && headerStr.includes('views') && count === 6) {
    return [25, 180, 75, 95, 60, 80] // sum = 515
  }

  // 4. Agent Tour Bookings / Appointments (7 columns): ['No', 'Property', 'Client Name', 'Contact', 'Format', 'Scheduled Time', 'Status']
  if (headerStr.includes('format') && headerStr.includes('scheduled time') && count === 7) {
    return [25, 135, 95, 80, 50, 80, 50] // sum = 515
  }

  // 5. Buyer Tours: Property, Host / Landlord, Phone, Date & Time, Format, Status
  if (headerStr.includes('host') && (headerStr.includes('phone') || headerStr.includes('contact')) && headerStr.includes('format')) {
    return [150, 95, 80, 95, 50, 45] // sum = 515
  }

  // 6. Buyer Favorites: Property, Location, Price (ETB), Type, Beds/Baths, Status
  if (headerStr.includes('beds/baths') || headerStr.includes('beds')) {
    return [135, 115, 85, 60, 65, 55] // sum = 515
  }

  // 7. Inquiries: Property / Subject, Host / Landlord, Last Message, Last Updated
  if (headerStr.includes('last message') || headerStr.includes('message')) {
    return [145, 105, 175, 90] // sum = 515
  }

  // 8. Alerts: Saved Search Name, Criteria Details, Notifications, Created Date
  if (headerStr.includes('criteria details') || headerStr.includes('criteria')) {
    return [140, 195, 90, 90] // sum = 515
  }

  // 9. Admin Users Directory: ['No', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined Date']
  if (headerStr.includes('email') && headerStr.includes('role')) {
    return [25, 95, 135, 85, 55, 50, 70] // sum = 515
  }

  // 10. Admin Properties Audit: ['No', 'Property Title', 'Type', 'Price (ETB)', 'Owner', 'Status', 'Date']
  if (headerStr.includes('property title') && headerStr.includes('owner')) {
    return [25, 140, 65, 85, 85, 55, 60] // sum = 515
  }

  // 11. Owner Tours Appointments: ['No', 'Property', 'Visitor / Client', 'Contact', 'Format', 'Date & Time', 'Status']
  if (headerStr.includes('visitor') && headerStr.includes('contact')) {
    return [25, 135, 95, 80, 50, 80, 50] // sum = 515
  }

  // 12. Owner Inquiries: ['No', 'Property', 'Client / Buyer', 'Last Activity Date']
  if (headerStr.includes('client / buyer') || (headerStr.includes('buyer') && count === 4)) {
    return [30, 185, 160, 140] // sum = 515
  }

  // 13. Owner Favorites: ['No', 'Property', 'Type', 'Price (ETB)', 'Saved By Client', 'Date Saved']
  if (headerStr.includes('saved by client')) {
    return [25, 145, 65, 85, 115, 80] // sum = 515
  }

  // 14. Owner Properties: ['No', 'Property', 'Type', 'Price (ETB)', 'Views', 'Saves', 'Tours', 'Status']
  if (headerStr.includes('saves') && headerStr.includes('tours')) {
    return [25, 155, 65, 80, 45, 45, 50, 50] // sum = 515
  }

  // 15. Generic fallbacks starting with 'No'
  const hasNo = headers[0]?.toLowerCase() === 'no' || headers[0] === '#'
  if (hasNo) {
    if (count === 8) return [25, 160, 65, 80, 45, 45, 45, 50]
    if (count === 7) return [25, 130, 95, 75, 55, 85, 50]
    if (count === 6) return [25, 130, 130, 80, 80, 70]
    if (count === 5) return [25, 185, 115, 110, 80]
    if (count === 4) return [30, 205, 150, 130]
  }

  // 16. Generic fallbacks without 'No'
  if (count === 7) return [130, 80, 80, 75, 50, 60, 40]
  if (count === 6) return [120, 110, 85, 75, 65, 60]
  if (count === 5) return [160, 105, 95, 90, 65]
  if (count === 4) return [175, 120, 120, 100]

  const w = Math.floor(515 / Math.max(1, count))
  return Array(count).fill(w)
}

function safeChars(text, colWidth) {
  const maxLen = Math.max(5, Math.floor((colWidth - 8) / 4.1))
  const str = String(text ?? '—').replace(/\s+/g, ' ').trim()
  if (str.length > maxLen) {
    return str.substring(0, maxLen - 2) + '..'
  }
  return str
}

/**
 * Pure JavaScript Compliant Binary PDF-1.4 Builder
 * Strictly formats and exports clean full-page tabular records with light aesthetic.
 */
export function generateValidPdfBlob({ title, subtitle, headers = [], rows = [] }) {
  const pdfParts = []
  const offsets = []
  let objCount = 0

  function escapePdf(str) {
    if (!str) return ''
    return String(str)
      .replace(/\\/g, '\\\\')
      .replace(/\(/g, '\\(')
      .replace(/\)/g, '\\)')
  }

  let colWidths = getClientColumnWidths(headers)
  // Ensure colWidths exactly matches headers length
  if (!Array.isArray(colWidths) || colWidths.length !== headers.length) {
    const defaultW = Math.floor(515 / Math.max(1, headers.length))
    colWidths = Array(headers.length).fill(defaultW)
  }

  const colStarts = []
  let curX = 40
  colWidths.forEach(w => {
    colStarts.push(curX)
    curX += Number(w || 60)
  })

  // 1. Build Stream Content (Clean Light Header, No Dark Banner)
  let stream = ''

  // Brand Title
  stream += 'BT\n/F1 16 Tf\n0.06 0.09 0.16 rg\n40 808 Td\n(BetLink Real Estate) Tj\nET\n'
  
  // Report Title
  stream += 'BT\n/F1 11 Tf\n0.18 0.23 0.32 rg\n40 790 Td\n'
  stream += `(${escapePdf(title)}) Tj\nET\n`

  // Subtitle & Timestamp
  stream += 'BT\n/F2 8 Tf\n0.45 0.52 0.62 rg\n40 774 Td\n'
  stream += `(${escapePdf(subtitle + '  |  Exported on ' + new Date().toLocaleString())}) Tj\nET\n`

  // Thin Header Divider Line
  stream += 'q\n0.82 0.86 0.92 RG\n0.75 w\n40 762 m 555 762 l S\nQ\n'

  // 2. Table Headers (Light Slate Pill Background)
  let tableY = 738
  if (headers && headers.length > 0) {
    stream += `q\n0.94 0.96 0.98 rg\n40 ${tableY - 4} 515 20 re\nf\nQ\n`
    stream += `q\n0.82 0.86 0.92 RG\n0.5 w\n40 ${tableY - 4} 515 20 re\nS\nQ\n`

    headers.forEach((h, idx) => {
      const xPos = colStarts[idx] + 4
      stream += `BT\n/F1 7.5 Tf\n0.2 0.25 0.35 rg\n${xPos} ${tableY + 3} Td\n`
      stream += `(${escapePdf(String(h).toUpperCase())}) Tj\nET\n`
    })

    tableY -= 20
  }

  // 3. Table Rows (Crisp typography with zero collision)
  if (rows && rows.length > 0) {
    rows.slice(0, 34).forEach((row) => {
      stream += `q\n0.9 0.92 0.95 RG\n0.5 w\n40 ${tableY - 3} m 555 ${tableY - 3} l S\nQ\n`

      row.forEach((cell, cIdx) => {
        const w = colWidths[cIdx] ?? 60
        const cellText = escapePdf(safeChars(cell, w))
        const font = cIdx === 0 ? '/F1 7.5 Tf\n' : '/F2 7.5 Tf\n'
        const xPos = colStarts[cIdx] + 4

        stream += 'BT\n'
        stream += font
        stream += '0.1 0.15 0.22 rg\n'
        stream += `${xPos} ${tableY + 3} Td\n`
        stream += `(${cellText}) Tj\nET\n`
      })

      tableY -= 19
    })
  }

  // 4. Footer
  stream += 'q\n0.85 0.88 0.92 RG\n0.5 w\n40 45 m 555 45 l S\nQ\n'
  stream += 'BT\n/F2 7.5 Tf\n0.5 0.55 0.62 rg\n40 30 Td\n'
  stream += `(BetLink Official Platform Audit Report  |  Document ID: ${Math.random().toString(36).substring(2, 10).toUpperCase()}  |  Page 1 of 1) Tj\nET\n`

  // Build Binary Structure
  pdfParts.push('%PDF-1.4\n%\xE2\xE3\xCF\xD3\n')

  function getByteLength(str) {
    if (typeof TextEncoder !== 'undefined') {
      return new TextEncoder().encode(str).length
    }
    return unescape(encodeURIComponent(str)).length
  }

  function addObj(content) {
    objCount++
    offsets.push(getByteLength(pdfParts.join('')))
    pdfParts.push(`${objCount} 0 obj\n${content}\nendobj\n`)
  }

  addObj('<< /Type /Catalog /Pages 2 0 R >>')
  addObj('<< /Type /Pages /Kids [3 0 R] /Count 1 >>')
  addObj('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>')
  addObj('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>')
  addObj('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>')
  addObj(`<< /Length ${getByteLength(stream)} >>\nstream\n${stream}\nendstream`)

  const startXref = getByteLength(pdfParts.join(''))
  pdfParts.push('xref\r\n')
  pdfParts.push(`0 ${objCount + 1}\r\n`)
  pdfParts.push('0000000000 65535 f \r\n')
  for (let i = 0; i < offsets.length; i++) {
    pdfParts.push(String(offsets[i]).padStart(10, '0') + ' 00000 n \r\n')
  }

  pdfParts.push('trailer\r\n')
  pdfParts.push(`<< /Size ${objCount + 1} /Root 1 0 R >>\r\n`)
  pdfParts.push('startxref\r\n')
  pdfParts.push(`${startXref}\r\n`)
  pdfParts.push('%%EOF')

  return new Blob([pdfParts.join('')], { type: 'application/pdf' })
}

export const reportService = {
  // ── 1. Owner Reports ───────────────────────────────────────────────────
  async getOwnerReport(params = {}) {
    try {
      const res = await api.get('/owner/reports/analytics', { params })
      if (res && res.data) return res
      if (res && res.summary) return { data: res }
    } catch (err) {
      console.warn('Failed to fetch live owner report:', err)
    }

    return {
      data: {
        summary: {
          totalProperties: 0,
          activeListings: 0,
          totalViews: 0,
          totalFavorites: 0,
          totalAppointments: 0,
          totalInquiries: 0,
          totalPortfolioValue: 0,
          inquiryRate: 0
        },
        propertyBreakdown: []
      }
    }
  },

  async exportOwnerReportExcel({ category = 'properties', property_type = 'all', time_range = 'all' } = {}) {
    const res = await this.getOwnerReport({ time_range })
    const data = res.data || {}

    if (category === 'appointments') {
      const list = data.appointmentsList || []
      const headers = ['Appointment ID', 'Property Title', 'Visitor / Client', 'Contact Phone', 'Format', 'Scheduled Date & Time', 'Status']
      const rows = list.map(a => [
        `#${a.id}`,
        a.property || 'Property Tour',
        a.visitor || 'Client',
        formatReportPhone(a.visitor_phone),
        a.type === 'virtual' ? 'Virtual Tour' : 'In-Person Tour',
        formatReportDate(a.scheduled_at),
        a.status ? (a.status.charAt(0).toUpperCase() + a.status.slice(1).toLowerCase()) : 'Pending'
      ])
      generateClientExcel({
        title: 'Property Tour Appointments Report',
        subtitle: 'Owner Scheduled Client Visits & Viewings Table',
        headers,
        rows,
        filename: `BetLink_Owner_Tour_Appointments_${Date.now()}.xls`
      })
      return true
    }

    if (category === 'inquiries') {
      const list = data.inquiriesList || []
      const headers = ['Inquiry ID', 'Property Title', 'Client Name', 'Last Activity Date']
      const rows = list.map(i => [
        `#${i.id}`,
        i.property || 'Property',
        i.buyer || 'Client',
        formatReportDate(i.updated_at)
      ])
      generateClientExcel({
        title: 'Property Client Inquiries Report',
        subtitle: 'Owner Lead Communications & Inquiry Activity Table',
        headers,
        rows,
        filename: `BetLink_Owner_Inquiries_Report_${Date.now()}.xls`
      })
      return true
    }

    if (category === 'favorites' || category === 'saved') {
      const list = data.favoritesList || []
      const headers = ['Save ID', 'Property Title', 'Property Type', 'Price (ETB)', 'Saved By Client', 'Client Email', 'Client Phone', 'Date Saved']
      const rows = list.map(f => [
        `#${f.id}`,
        f.property || 'Property',
        f.property_type || 'Apartment',
        Number(f.property_price || 0).toLocaleString(),
        f.user_name || 'Client',
        f.user_email || '—',
        formatReportPhone(f.user_phone),
        formatReportDate(f.saved_at)
      ])
      generateClientExcel({
        title: 'Property Favorites & Saved Interest Report',
        subtitle: 'Interested Buyers Who Bookmarked Properties Table',
        headers,
        rows,
        filename: `BetLink_Owner_Saved_Properties_${Date.now()}.xls`
      })
      return true
    }

    // Default: properties
    let list = data.propertyBreakdown || []
    if (property_type && property_type !== 'all') {
      list = list.filter(p => p.type?.toLowerCase() === property_type.toLowerCase())
    }

    const headers = ['Property ID', 'Property Title', 'Category', 'Listing Type', 'Price (ETB)', 'Views', 'Favorites', 'Tours Booked', 'Status', 'Date Listed']
    const rows = list.map(p => [
      `#${p.id}`,
      p.title || 'Property',
      p.type || 'Residential',
      p.listing_type || 'Sale',
      Number(p.price || 0).toLocaleString(),
      p.views || 0,
      p.favorites || 0,
      p.tours_booked || 0,
      p.status ? (p.status.charAt(0).toUpperCase() + p.status.slice(1).toLowerCase()) : 'Active',
      formatReportDate(p.created_at)
    ])
    const fileSuffix = property_type !== 'all' ? `_${property_type}` : ''
    generateClientExcel({
      title: property_type !== 'all' ? `Owner ${property_type} Listings Report` : 'Property Owner Portfolio Report',
      subtitle: 'Personal Portfolio Performance & Status Records',
      headers,
      rows,
      filename: `BetLink_Owner_Properties${fileSuffix}_Report_${Date.now()}.xls`
    })
    return true
  },

  async exportOwnerReportPdf({ category = 'properties', property_type = 'all', time_range = 'all' } = {}) {
    const res = await this.getOwnerReport({ time_range })
    const data = res.data || {}

    if (category === 'appointments') {
      const headers = ['No', 'Property', 'Visitor / Client', 'Contact', 'Format', 'Date & Time', 'Status']
      const rows = (data.appointmentsList || []).map((a, idx) => [
        idx + 1,
        a.property,
        a.visitor,
        a.visitor_phone,
        a.type,
        a.scheduled_at,
        a.status
      ])
      const blob = generateValidPdfBlob({
        title: 'Property Tour Bookings Report',
        subtitle: 'Scheduled Client Viewings Table Records',
        headers,
        rows
      })
      downloadBlob(blob, `BetLink_Owner_Tour_Appointments_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    if (category === 'inquiries') {
      const headers = ['No', 'Property', 'Client / Buyer', 'Last Activity Date']
      const rows = (data.inquiriesList || []).map((i, idx) => [
        idx + 1,
        i.property,
        i.buyer,
        i.updated_at
      ])
      const blob = generateValidPdfBlob({
        title: 'Property Client Inquiries Report',
        subtitle: 'Lead Communications & Inquiry Activity Table',
        headers,
        rows
      })
      downloadBlob(blob, `BetLink_Owner_Inquiries_Report_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    if (category === 'favorites' || category === 'saved') {
      const headers = ['No', 'Property', 'Type', 'Price (ETB)', 'Saved By Client', 'Date Saved']
      const rows = (data.favoritesList || []).map((f, idx) => [
        idx + 1,
        f.property,
        f.property_type,
        Number(f.property_price || 0).toLocaleString(),
        f.user_name,
        f.saved_at ? f.saved_at.substring(0, 10) : '—'
      ])
      const blob = generateValidPdfBlob({
        title: 'Property Favorites & Saved Interest Report',
        subtitle: 'Interested Buyers Who Bookmarked Properties Table Records',
        headers,
        rows
      })
      downloadBlob(blob, `BetLink_Owner_Saved_Properties_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    // Default: properties
    let list = data.propertyBreakdown || []
    if (property_type && property_type !== 'all') {
      list = list.filter(p => p.type?.toLowerCase() === property_type.toLowerCase())
    }

    const headers = ['No', 'Property', 'Type', 'Price (ETB)', 'Views', 'Saves', 'Tours', 'Status']
    const rows = list.map((p, idx) => [
      idx + 1,
      p.title,
      p.type,
      Number(p.price).toLocaleString(),
      p.views,
      p.favorites,
      p.tours_booked,
      p.status
    ])

    const typeSubtitle = property_type !== 'all' ? `${property_type} Portfolio Records` : 'Personal Portfolio & Performance Table Records'
    const blob = generateValidPdfBlob({
      title: property_type !== 'all' ? `Owner ${property_type} Listings Report` : 'Property Owner Listings Report',
      subtitle: typeSubtitle,
      headers,
      rows
    })

    const fileSuffix = property_type !== 'all' ? `_${property_type}` : ''
    downloadBlob(blob, `BetLink_Owner_Properties${fileSuffix}_Report_${Date.now()}.pdf`, 'application/pdf')
    return true
  },

  // ── 3. Buyer Reports ───────────────────────────────────────────────────
  async getBuyerReport(params = {}) {
    try {
      const res = await api.get('/buyer/reports', { params })
      if (res && res.data) return res
      if (res && res.summary) return { data: res }
    } catch (err) {
      console.warn('Failed to fetch live buyer report from primary endpoint, trying analytics:', err)
      try {
        const res2 = await api.get('/buyer/reports/analytics', { params })
        if (res2 && res2.data) return res2
      } catch (err2) {
        console.warn('Backend reports endpoint fallback:', err2)
      }
    }

    return {
      data: {
        summary: {
          savedFavorites: 0,
          totalAppointments: 0,
          confirmedAppointments: 0,
          completedAppointments: 0,
          activeConversations: 0,
          savedSearches: 0
        },
        toursHistory: [],
        favorites: [],
        conversations: [],
        saved_searches: []
      }
    }
  },

  async exportBuyerReportExcel(records = [], section = 'tours') {
    if (section === 'favorites') {
      const headers = ['Property ID', 'Property Title', 'Location', 'Price (ETB)', 'Listing Type', 'Bedrooms', 'Bathrooms', 'Status', 'Date Saved']
      const rows = records.map(f => [
        `#${f.id}`,
        f.title || 'Saved Property',
        f.location || 'Addis Ababa',
        Number(f.price || 0).toLocaleString(),
        f.listing_type || 'Sale',
        f.bedrooms || '—',
        f.bathrooms || '—',
        f.status || 'Active',
        f.saved_at ? f.saved_at.substring(0, 10) : '—'
      ])
      generateClientExcel({
        title: 'Saved Favorites Property Report',
        subtitle: 'Personal Shortlisted Properties & Price Table',
        headers,
        rows,
        filename: `BetLink_Saved_Favorites_Report_${Date.now()}.xls`
      })
      return true
    }

    if (section === 'inquiries') {
      const headers = ['Inquiry ID', 'Property / Subject', 'Host / Landlord', 'Host Email', 'Last Message', 'Updated Time']
      const rows = records.map(c => [
        `#${c.id}`,
        c.property || 'General Inquiry',
        c.contact_name || 'Landlord',
        c.contact_email || '—',
        c.last_message || '—',
        formatReportDate(c.updated_at)
      ])
      generateClientExcel({
        title: 'Host Inquiries & Communications Report',
        subtitle: 'Landlord & Host Message Communication Records',
        headers,
        rows,
        filename: `BetLink_Inquiries_Report_${Date.now()}.xls`
      })
      return true
    }

    if (section === 'alerts') {
      const headers = ['Alert ID', 'Search Name', 'Criteria Details', 'Email Notifications', 'Date Created']
      const rows = records.map(s => [
        `#${s.id}`,
        s.name || 'Custom Search',
        JSON.stringify(s.filters || {}),
        s.alert_enabled ? 'Active' : 'Disabled',
        formatReportDate(s.created_at)
      ])
      generateClientExcel({
        title: 'Saved Alerts & Custom Search Report',
        subtitle: 'Buyer Property Notification Presets Table',
        headers,
        rows,
        filename: `BetLink_Saved_Alerts_Report_${Date.now()}.xls`
      })
      return true
    }

    // Default: Tours / Appointments
    const headers = ['Appointment ID', 'Property Title', 'Host / Landlord', 'Contact Phone', 'Scheduled Time', 'Tour Type', 'Status']
    const rows = records.map(t => [
      `#${t.id}`,
      t.property || 'Property Tour',
      t.host || 'Property Host',
      formatReportPhone(t.host_phone),
      formatReportDate(t.scheduled_at),
      t.type === 'virtual' ? 'Virtual Tour' : 'In-Person Tour',
      t.status ? (t.status.charAt(0).toUpperCase() + t.status.slice(1).toLowerCase()) : 'Pending'
    ])

    generateClientExcel({
      title: 'Tour Bookings & Appointments Report',
      subtitle: 'Personal Property Viewings & Scheduled Visits Table',
      headers,
      rows,
      filename: `BetLink_Tour_Appointments_Report_${Date.now()}.xls`
    })
    return true
  },

  async exportBuyerReportPdf(records = [], section = 'tours') {
    if (section === 'favorites') {
      const headers = ['Property', 'Location', 'Price (ETB)', 'Type', 'Beds/Baths', 'Status']
      const rows = records.map(f => [
        f.title,
        f.location,
        Number(f.price || 0).toLocaleString(),
        f.listing_type,
        `${f.bedrooms || '—'} / ${f.bathrooms || '—'}`,
        f.status
      ])

      const blob = generateValidPdfBlob({
        title: 'Saved Favorites Property Report',
        subtitle: 'Personal Shortlisted Properties & Price Table',
        headers,
        rows
      })

      downloadBlob(blob, `BetLink_Saved_Favorites_Report_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    if (section === 'inquiries') {
      const headers = ['Property / Subject', 'Host / Landlord', 'Last Message', 'Last Updated']
      const rows = records.map(c => [
        c.property,
        c.contact_name,
        c.last_message,
        formatReportDate(c.updated_at)
      ])

      const blob = generateValidPdfBlob({
        title: 'Host Inquiries & Conversations Report',
        subtitle: 'Landlord & Host Message Communication Records',
        headers,
        rows
      })

      downloadBlob(blob, `BetLink_Inquiries_Report_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    if (section === 'alerts') {
      const headers = ['Saved Search Name', 'Criteria Details', 'Notifications', 'Created Date']
      const rows = records.map(s => [
        s.name,
        JSON.stringify(s.filters || {}),
        s.alert_enabled ? 'Active' : 'Disabled',
        formatReportDate(s.created_at)
      ])

      const blob = generateValidPdfBlob({
        title: 'Saved Alerts & Custom Search Report',
        subtitle: 'Buyer Property Notification Presets Table',
        headers,
        rows
      })

      downloadBlob(blob, `BetLink_Saved_Alerts_Report_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    // Default: Tours
    const headers = ['Property', 'Host / Landlord', 'Phone', 'Date & Time', 'Format', 'Status']
    const rows = records.map(t => [
      t.property || 'Property Tour',
      t.host || 'Property Host',
      formatReportPhone(t.host_phone),
      formatReportDate(t.scheduled_at),
      t.type === 'virtual' ? 'Virtual' : 'In-Person',
      t.status ? (t.status.charAt(0).toUpperCase() + t.status.slice(1).toLowerCase()) : 'Pending'
    ])

    const blob = generateValidPdfBlob({
      title: 'Tour Bookings & Activity Report',
      subtitle: 'Personal Property Viewings & Appointments Table',
      headers,
      rows
    })

    downloadBlob(blob, `BetLink_Tour_Appointments_Report_${Date.now()}.pdf`, 'application/pdf')
    return true
  },

  // ── 4. Agent Reports ───────────────────────────────────────────────────
  async getAgentReport(params = {}) {
    try {
      const res = await api.get('/agent/reports/analytics', { params })
      if (res && res.data) return res
      if (res && res.summary) return { data: res }
    } catch (err) {
      console.warn('Failed to fetch live agent report:', err)
    }

    return {
      data: {
        summary: {
          assignedProperties: 0,
          activeListings: 0,
          totalLeads: 0,
          tourRequests: 0,
          dealsClosed: 0,
          estimatedCommissions: 0,
          conversionRate: 0
        },
        leadsBreakdown: []
      }
    }
  },

  async exportAgentReportExcel(params = {}) {
    const res = await this.getAgentReport(params)
    const data = res.data || {}
    const category = params.category || 'leads'

    if (category === 'properties' || category === 'listings') {
      const list = data.propertyBreakdown || []
      const headers = ['No', 'Property ID', 'Title', 'Category', 'Listing Type', 'Price (ETB)', 'Views', 'Status', 'Date Listed']
      const rows = list.map((p, idx) => [
        idx + 1,
        `#${p.id}`,
        p.title || 'Property',
        p.type || 'Residential',
        p.listing_type || 'Sale',
        Number(p.price || 0).toLocaleString(),
        p.views || 0,
        p.status ? (p.status.charAt(0).toUpperCase() + p.status.slice(1).toLowerCase()) : 'Active',
        formatReportDate(p.created_at)
      ])
      generateClientExcel({
        title: 'Agent Managed Portfolio Report',
        subtitle: 'Assigned Real Estate Listings & Performance Metrics',
        headers,
        rows,
        filename: `BetLink_Agent_Managed_Listings_${Date.now()}.xls`
      })
      return true
    }

    if (category === 'appointments' || category === 'tours') {
      const list = data.appointmentsList || []
      const headers = ['No', 'Appointment ID', 'Property Title', 'Client Name', 'Contact Phone', 'Format', 'Scheduled Time', 'Status']
      const rows = list.map((a, idx) => [
        idx + 1,
        `#${a.id}`,
        a.property || 'Property Tour',
        a.visitor || 'Client',
        formatReportPhone(a.visitor_phone),
        a.type || 'In-Person',
        formatReportDate(a.scheduled_at),
        a.status ? (a.status.charAt(0).toUpperCase() + a.status.slice(1).toLowerCase()) : 'Pending'
      ])
      generateClientExcel({
        title: 'Agent Tour Bookings & Visits Report',
        subtitle: 'Scheduled Property Viewings & Appointments Dataset',
        headers,
        rows,
        filename: `BetLink_Agent_Tour_Appointments_${Date.now()}.xls`
      })
      return true
    }

    // Default: leads
    const list = data.leadsBreakdown || []
    const headers = ['No', 'Lead ID', 'Client Name', 'Inquired Property', 'Listing Type', 'Pipeline Stage', 'Activity Date']
    const rows = list.map((l, idx) => [
      idx + 1,
      `#${l.id}`,
      l.client || 'Client',
      l.property || 'Property',
      l.listing_type || 'Sale',
      l.status ? (l.status.charAt(0).toUpperCase() + l.status.slice(1).toLowerCase()) : 'Lead',
      formatReportDate(l.date)
    ])
    generateClientExcel({
      title: 'Agent Client Leads Pipeline Report',
      subtitle: 'Inquiries, Pipeline Stages & Client Activity Table',
      headers,
      rows,
      filename: `BetLink_Agent_Client_Leads_${Date.now()}.xls`
    })
    return true
  },

  async exportAgentReportPdf(params = {}) {
    const res = await this.getAgentReport(params)
    const data = res.data || {}
    const category = params.category || 'leads'

    if (category === 'properties' || category === 'listings') {
      const headers = ['No', 'Property Title', 'Type', 'Price (ETB)', 'Views', 'Status']
      const rows = (data.propertyBreakdown || []).map((p, idx) => [
        idx + 1,
        p.title || 'Property',
        p.type || 'Residential',
        Number(p.price || 0).toLocaleString(),
        p.views || 0,
        p.status || 'Active'
      ])
      if (rows.length === 0) {
        rows.push([1, 'No listings recorded', '—', '0', '0', 'Active'])
      }
      const blob = generateValidPdfBlob({
        title: 'Agent Managed Portfolio Report',
        subtitle: 'Assigned Real Estate Listings & Performance Metrics',
        headers,
        rows
      })
      downloadBlob(blob, `BetLink_Agent_Managed_Listings_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    if (category === 'appointments' || category === 'tours') {
      const headers = ['No', 'Property', 'Client Name', 'Contact', 'Format', 'Scheduled Time', 'Status']
      const rows = (data.appointmentsList || []).map((a, idx) => [
        idx + 1,
        a.property || 'Property Tour',
        a.visitor || 'Client',
        formatReportPhone(a.visitor_phone),
        a.type || 'In-Person',
        formatReportDate(a.scheduled_at),
        a.status || 'Pending'
      ])
      if (rows.length === 0) {
        rows.push([1, 'No scheduled tours', '—', '—', '—', '—', 'Pending'])
      }
      const blob = generateValidPdfBlob({
        title: 'Agent Tour Bookings & Visits Report',
        subtitle: 'Scheduled Property Viewings & Appointments Table',
        headers,
        rows
      })
      downloadBlob(blob, `BetLink_Agent_Tour_Appointments_${Date.now()}.pdf`, 'application/pdf')
      return true
    }

    // Default: leads
    const headers = ['No', 'Client Name', 'Inquired Property', 'Listing Type', 'Pipeline Status', 'Date']
    const rows = (data.leadsBreakdown || []).map((l, idx) => [
      idx + 1,
      l.client || 'Client',
      l.property || 'Property',
      l.listing_type || 'Sale',
      l.status || 'Lead',
      formatReportDate(l.date)
    ])
    if (rows.length === 0) {
      rows.push([1, 'No client leads recorded yet', '—', '—', '—', 'Active'])
    }
    const blob = generateValidPdfBlob({
      title: 'Agent Client Leads Pipeline Report',
      subtitle: 'Inquiries, Pipeline Stages & Client Activity Table',
      headers,
      rows
    })
    downloadBlob(blob, `BetLink_Agent_Client_Leads_${Date.now()}.pdf`, 'application/pdf')
    return true
  },

  // ── 5. Admin System Reports & Analytics ─────────────────────────────────
  async getAdminReport(params = {}) {
    try {
      const res = await api.get('/admin/analytics-reports', { params })
      if (res && res.data) return res
      if (res && res.summary) return { data: res }
    } catch (err) {
      console.warn('Failed to fetch live admin report, checking alternative endpoint:', err)
      try {
        const altRes = await api.get('/admin/reports/analytics', { params })
        if (altRes && altRes.data) return altRes
        if (altRes && altRes.summary) return { data: altRes }
      } catch (e) {
        console.error('Failed to fetch admin report from both endpoints:', e)
      }
    }

    return {
      data: {
        summary: {
          totalUsers: 0,
          totalProperties: 0,
          activeProperties: 0,
          pendingProperties: 0,
          totalAppointments: 0,
          totalInquiries: 0,
          totalRevenue: 0,
          activeAgents: 0
        },
        recentProperties: [],
        usersList: []
      }
    }
  },

  // ── Abuse / Fraud Reports API ──
  async getAbuseReports(params = {}) {
    try {
      const res = await api.get('/admin/reports', { params })
      return res
    } catch (err) {
      console.error('Failed to fetch abuse reports:', err)
      throw err
    }
  },

  async updateAbuseReport(id, payload) {
    try {
      const res = await api.put(`/admin/reports/${id}`, payload)
      return res
    } catch (err) {
      console.error('Failed to update abuse report:', err)
      throw err
    }
  },

  async exportAdminAbuseReportsExcel(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAbuseReports({ per_page: 200 })
      list = res.data?.data || res.data || []
    }
    const headers = ['No', 'Report ID', 'Reported Target', 'Target Type', 'Reported By', 'Email', 'Reason', 'Description', 'Status', 'Date Filed']
    const rows = list.map((r, idx) => {
      let targetName = `ID #${r.reportable_id}`
      if (r.reportable?.title) targetName = r.reportable.title
      else if (r.reportable?.name) targetName = r.reportable.name
      const targetType = r.reportable_type ? r.reportable_type.split('\\').pop() : 'Entity'

      return [
        idx + 1,
        `#${r.id}`,
        targetName,
        targetType,
        r.reporter?.name || 'Anonymous',
        r.reporter?.email || '—',
        r.reason ? (r.reason.charAt(0).toUpperCase() + r.reason.slice(1).toLowerCase()) : 'Flagged',
        r.description || '—',
        r.status ? (r.status.charAt(0).toUpperCase() + r.status.slice(1).toLowerCase()) : 'Pending',
        formatReportDate(r.created_at)
      ]
    })

    generateClientExcel({
      title: 'Fraud & Abuse Complaints Report',
      subtitle: 'Official BetLink Safety & Trust Moderation Dataset',
      headers,
      rows,
      filename: `BetLink_Admin_Abuse_Reports_${Date.now()}.xls`
    })
    return true
  },

  async exportAdminAbuseReportsPdf(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAbuseReports({ per_page: 200 })
      list = res.data?.data || res.data || []
    }
    const headers = ['No', 'Reported Item', 'Reported By', 'Reason', 'Status', 'Date Filed']
    const rows = list.map((r, idx) => {
      let targetName = `ID #${r.reportable_id}`
      if (r.reportable?.title) targetName = r.reportable.title
      else if (r.reportable?.name) targetName = r.reportable.name

      return [
        idx + 1,
        targetName,
        r.reporter?.name || 'User',
        r.reason || 'Flagged',
        r.status || 'Pending',
        formatReportDate(r.created_at)
      ]
    })

    const blob = generateValidPdfBlob({
      title: 'Safety & Abuse Complaints Report',
      subtitle: 'Official BetLink Safety & Trust Moderation Dataset',
      headers,
      rows
    })

    downloadBlob(blob, `BetLink_Admin_Abuse_Reports_${Date.now()}.pdf`, 'application/pdf')
    return true
  },

  async exportAdminUsersExcel(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAdminReport()
      list = res.data?.usersList || []
    }
    const headers = ['No', 'User ID', 'Full Name', 'Email Address', 'Phone Number', 'Role', 'Status', 'Joined Date']
    const rows = list.map((u, idx) => [
      idx + 1,
      `#${u.id}`,
      u.name || 'User',
      u.email || '—',
      formatReportPhone(u.phone),
      u.role ? (u.role.charAt(0).toUpperCase() + u.role.slice(1).toLowerCase()) : 'User',
      u.status ? (u.status.charAt(0).toUpperCase() + u.status.slice(1).toLowerCase()) : 'Active',
      formatReportDate(u.created_at)
    ])
    generateClientExcel({
      title: 'Registered Users Directory Report',
      subtitle: 'Platform Members & Account Registry Dataset',
      headers,
      rows,
      filename: `BetLink_Admin_Users_Directory_${Date.now()}.xls`
    })
    return true
  },

  async exportAdminUsersPdf(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAdminReport()
      list = res.data?.usersList || []
    }
    const headers = ['No', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Joined Date']
    const rows = list.map((u, idx) => [
      idx + 1,
      u.name,
      u.email,
      u.phone || '—',
      u.role,
      u.status,
      u.created_at
    ])

    const blob = generateValidPdfBlob({
      title: 'Platform Users Directory Report',
      subtitle: 'Official BetLink User Accounts Audit Dataset',
      headers,
      rows
    })

    downloadBlob(blob, `BetLink_Admin_Users_Report_${Date.now()}.pdf`, 'application/pdf')
    return true
  },

  async exportAdminPropertiesExcel(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAdminReport()
      list = res.data?.recentProperties || []
    }
    const headers = ['No', 'Property ID', 'Property Title', 'Category', 'Listing Type', 'Price (ETB)', 'Owner', 'Status', 'Views', 'Date Listed']
    const rows = list.map((p, idx) => [
      idx + 1,
      `#${p.id}`,
      p.title || 'Property',
      p.type || 'Residential',
      p.listing_type || 'Sale',
      Number(p.price || 0).toLocaleString(),
      p.owner || 'Landlord',
      p.status ? (p.status.charAt(0).toUpperCase() + p.status.slice(1).toLowerCase()) : 'Active',
      p.views || 0,
      formatReportDate(p.created_at)
    ])
    generateClientExcel({
      title: 'Platform Properties Audit Report',
      subtitle: 'Comprehensive Listings Inventory & Moderation Dataset',
      headers,
      rows,
      filename: `BetLink_Admin_Properties_Audit_${Date.now()}.xls`
    })
    return true
  },

  async exportAdminPropertiesPdf(customList = null) {
    let list = customList
    if (!list) {
      const res = await this.getAdminReport()
      list = res.data?.recentProperties || []
    }
    const headers = ['No', 'Property Title', 'Type', 'Price (ETB)', 'Owner', 'Status', 'Date']
    const rows = list.map((p, idx) => [
      idx + 1,
      p.title,
      p.type,
      Number(p.price || 0).toLocaleString(),
      p.owner,
      p.status,
      p.created_at
    ])

    const blob = generateValidPdfBlob({
      title: 'Platform Properties Audit Report',
      subtitle: 'Comprehensive Listings Inventory & Moderation Dataset',
      headers,
      rows
    })

    downloadBlob(blob, `BetLink_Admin_Properties_Report_${Date.now()}.pdf`, 'application/pdf')
    return true
  }
}

export default reportService
